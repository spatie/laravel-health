<?php

namespace Spatie\Health\ResultStores;

use Exception;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Spatie\Health\Checks\Result;
use Spatie\Health\ResultStores\Report\Report;
use Spatie\Health\ResultStores\Report\ReportedCheck;

class JsonFileHealthResultStore implements ResultStore
{
    protected FilesystemAdapter $disk;
    protected string $path;

    public function __construct(string $diskName, string $path)
    {
        $this->disk = Storage::disk($diskName);

        $this->path = $path;
    }

    /** @param Collection<int, \Spatie\Health\Checks\Result> $checkResults */
    public function save(Collection $checkResults): void
    {
        $report = new Report(now());

        $checkResults
            ->map(function (Result $result) {
                return new ReportedCheck(
                    name: $result->check->getName(),
                    message: $result->getMessage(),
                    status: (string)$result->status->value,
                    meta: $result->meta,
                );
            })
            ->each(function (ReportedCheck $check) use ($report) {
                $report->addCheck($check);
            });

        $contents = $report->toJson();

        $this->disk->write($this->path, $contents);
    }

    public function latestReport(): ?Report
    {
        $content = null;

        try {
            $content = $this->disk->read($this->path);
        } catch (Exception $exception) {
            report($exception);
        }

        if (! $content) {
            return null;
        }

        return Report::fromJson($content);
    }
}
