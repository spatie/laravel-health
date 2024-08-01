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
        $files = collect($this->disk ? $files = $this->disk->files($this->locatedAt) : File::glob($this->locatedAt));

        if ($files->isEmpty()) {
            return Result::make()->failed('No backups found');
        }

        $eligableBackups = $files
            ->map(function (string $path) {
                return new BackupFile($path, $this->disk, $this->parseModifiedUsing);
            });

        if ($this->minimumNumberOfBackups) {
            if ($eligableBackups->count() < $this->minimumNumberOfBackups) {
                return Result::make()->failed('Not enough backups found');
            }
        }

        if ($this->maximumNumberOfBackups) {
            if ($eligableBackups->count() > $this->maximumNumberOfBackups) {
                return Result::make()->failed('Too many backups found');
            }
        }

        $youngestBackup = $this->getYoungestBackup($eligableBackups);

        if ($this->youngestShouldHaveBeenMadeBefore) {
            if (!$youngestBackup || $this->youngestBackupIsToolOld($youngestBackup)) {
                return Result::make()
                    ->failed('Youngest backup was too old');
            }
        }

        $oldestBackup = $this->getOldestBackup($eligableBackups);

        if ($this->oldestShouldHaveBeenMadeAfter) {
            if (!$oldestBackup || $this->oldestBackupIsTooYoung($oldestBackup)) {
                return Result::make()
                    ->failed('Oldest backup was too young');
            }
        }

        if ($eligableBackups->isEmpty()) {
            return Result::make()->failed('Backups are not large enough');
        }

        if ($this->onlyCheckSizeOnFirstAndLast) {
            $eligableBackups = collect([$youngestBackup, $oldestBackup]);
        }

        if (
            $eligableBackups->filter(function (BackupFile $file) {
                return $file->size() >= $this->minimumSizeInMegabytes * 1024 * 1024;
            })->isEmpty()
        ) {
            return Result::make()->failed('Backups are not large enough');
        }

        return Result::make()->ok();
    }

    protected function getYoungestBackup(Collection $backups): ?BackupFile
    {
        return $backups
            ->sortByDesc(fn (BackupFile $file) => $file->lastModified())
            ->first();
    }

    protected function youngestBackupIsToolOld(BackupFile $youngestBackup): bool
    {
        $threshold = $this->youngestShouldHaveBeenMadeBefore->getTimestamp();

        return $youngestBackup->lastModified() <= $threshold;
    }

    protected function getOldestBackup(Collection $backups): ?BackupFile
    {
        return $backups
            ->sortBy(fn (BackupFile $file) => $file->lastModified())
            ->first();

    }
    protected function oldestBackupIsTooYoung(BackupFile $oldestBackup): bool
    {
        $threshold = $this->oldestShouldHaveBeenMadeAfter->getTimestamp();

        return $oldestBackup->lastModified() >= $threshold;
    }
}
