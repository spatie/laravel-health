<?php

namespace Spatie\Health\Checks\Checks;

use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;
use Spatie\Health\Support\BackupFile;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;

class BackupsCheck extends Check
{
    protected ?string $locatedAt = null;

    protected ?Filesystem $disk = null;

    protected ?Carbon $youngestShouldHaveBeenMadeBefore = null;

    protected ?Carbon $oldestShouldHaveBeenMadeAfter = null;

    protected ?string $parseModifiedUsing = null;

    protected int $minimumSizeInMegabytes = 0;

    protected bool $onlyCheckSizeOnFirstAndLast = false;

    protected ?int $minimumNumberOfBackups = null;

    protected ?int $maximumNumberOfBackups = null;

    public function locatedAt(string $globPath): self
    {
        $this->locatedAt = $globPath;

        return $this;
    }

    public function onDisk($disk)
    {
        $this->disk = Storage::disk($disk);

        return $this;
    }

    public function parseModifiedFormat($parseModifiedFormat = 'Y-m-d_H-i-s'): self
    {
        $this->parseModifiedUsing = $parseModifiedFormat;

        return $this;
    }

    public function youngestBackShouldHaveBeenMadeBefore(Carbon $date): self
    {
        $this->youngestShouldHaveBeenMadeBefore = $date;

        return $this;
    }

    public function oldestBackShouldHaveBeenMadeAfter(Carbon $date): self
    {
        $this->oldestShouldHaveBeenMadeAfter = $date;

        return $this;
    }

    public function atLeastSizeInMb(int $minimumSizeInMegabytes, $onlyCheckFirstAndLast = false): self
    {
        $this->minimumSizeInMegabytes = $minimumSizeInMegabytes;

        return $this;
    }

    public function onlyCheckSizeOnFirstAndLast($onlyCheckSizeOnFirstAndLast = true): self
    {
        $this->onlyCheckSizeOnFirstAndLast = $onlyCheckSizeOnFirstAndLast;

        return $this;
    }

    public function numberOfBackups(?int $min = null, ?int $max = null): self
    {
        $this->minimumNumberOfBackups = $min;
        $this->maximumNumberOfBackups = $max;

        return $this;
    }

    public function run(): Result
    {
        $eligibleBackups = $this->getBackupFiles();

        $backupCount = $eligibleBackups->count();

        $result = Result::make()->meta([
            'minimum_size' => $this->minimumSizeInMegabytes.'MB',
            'backup_count' => $backupCount,
        ]);

        if ($backupCount === 0) {
            return $result->failed('No backups found');
        }

        if ($this->minimumNumberOfBackups && $backupCount < $this->minimumNumberOfBackups) {
            return $result->failed('Not enough backups found');
        }

        if ($this->maximumNumberOfBackups && $backupCount > $this->maximumNumberOfBackups) {
            return $result->failed('Too many backups found');
        }

        $youngestBackup = $this->getYoungestBackup($eligibleBackups);
        $oldestBackup = $this->getOldestBackup($eligibleBackups);

        $result->appendMeta([
            'youngest_backup' => Carbon::createFromTimestamp($youngestBackup?->lastModified())->toDateTimeString(),
            'oldest_backup'   => Carbon::createFromTimestamp($oldestBackup?->lastModified())->toDateTimeString(),
        ]);

        if ($this->youngestShouldHaveBeenMadeBefore && $this->youngestBackupIsToolOld($youngestBackup)) {
            return $result->failed('The youngest backup was too old');
        }

        if ($this->oldestShouldHaveBeenMadeAfter && $this->oldestBackupIsTooYoung($oldestBackup)) {
            return $result->failed('The oldest backup was too young');
        }

        $backupsToCheckSizeOn = $this->onlyCheckSizeOnFirstAndLast
            ? collect([$youngestBackup, $oldestBackup])
            : $eligibleBackups;

        if ($backupsToCheckSizeOn->filter(
            fn(BackupFile $file) => $file->size() >= $this->minimumSizeInMegabytes * 1024 * 1024
        )->isEmpty()) {
            return $result->failed('Backups are not large enough');
        }

        return $result->ok();
    }

    protected function getBackupFiles(): Collection
    {
        return collect(
            $this->disk
                ? $this->disk->files($this->locatedAt)
                : File::glob($this->locatedAt)
        )->map(function (string $path) {
            return new BackupFile($path, $this->disk, $this->parseModifiedUsing);
        });
    }

    protected function getYoungestBackup(Collection $backups): ?BackupFile
    {
        return $backups
            ->sortByDesc(fn (BackupFile $file) => $file->lastModified())
            ->first();
    }

    protected function youngestBackupIsToolOld(?BackupFile $youngestBackup): bool
    {
        $threshold = $this->youngestShouldHaveBeenMadeBefore->getTimestamp();

        return !$youngestBackup || $youngestBackup->lastModified() <= $threshold;
    }

    protected function getOldestBackup(Collection $backups): ?BackupFile
    {
        return $backups
            ->sortBy(fn (BackupFile $file) => $file->lastModified())
            ->first();

    }
    protected function oldestBackupIsTooYoung(?BackupFile $oldestBackup): bool
    {
        $threshold = $this->oldestShouldHaveBeenMadeAfter->getTimestamp();

        return !$oldestBackup || $oldestBackup->lastModified() >= $threshold;
    }
}
