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

    protected int $minimumSizeInMegabytes = 0;

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

    public function atLeastSizeInMb(int $minimumSizeInMegabytes): self
    {
        $this->minimumSizeInMegabytes = $minimumSizeInMegabytes;

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
                return new BackupFile($path, $this->disk);
            })
            ->filter(function (BackupFile $file) {
                return $file->size() >= $this->minimumSizeInMegabytes * 1024 * 1024;
            });

        if ($eligableBackups->isEmpty()) {
            return Result::make()->failed('No backups found that are large enough');
        }

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

        if ($this->youngestShouldHaveBeenMadeBefore) {
            if ($this->youngestBackupIsToolOld($eligableBackups)) {
                return Result::make()
                             ->failed('Youngest backup was too old');
            }
        }

        if ($this->oldestShouldHaveBeenMadeAfter) {
            if ($this->oldestBackupIsTooYoung($eligableBackups)) {
                return Result::make()
                             ->failed('Oldest backup was too young');
            }
        }

        return Result::make()->ok();
    }

    /**
     * @param  Collection<SymfonyFile>  $backups
     */
    protected function youngestBackupIsToolOld(Collection $backups): bool
    {
        /** @var SymfonyFile|null $youngestBackup */
        $youngestBackup = $backups
            ->sortByDesc(fn (BackupFile $file) => $file->lastModified())
            ->first();

        $threshold = $this->youngestShouldHaveBeenMadeBefore->getTimestamp();

        return $youngestBackup->lastModified() <= $threshold;
    }

    /**
     * @param  Collection<SymfonyFile>  $backups
     */
    protected function oldestBackupIsTooYoung(Collection $backups): bool
    {
        /** @var SymfonyFile|null $oldestBackup */
        $oldestBackup = $backups
            ->sortBy(fn (BackupFile $file) => $file->lastModified())
            ->first();

        $threshold = $this->oldestShouldHaveBeenMadeAfter->getTimestamp();

        return $oldestBackup->lastModified() >= $threshold;
    }
}
