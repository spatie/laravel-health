<?php

namespace Spatie\Health\ResultStores;

use Exception;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use OhDear\HealthCheckReport\Line;
use OhDear\HealthCheckReport\Report;
use Spatie\Health\Support\Result;

class JsonFileHealthResultStore implements ResultStore
{
    protected Filesystem $disk;
    protected string $path;

    public function __construct(string $diskName, string $path)
    {
        $this->disk = Storage::disk($diskName);

        $this->path = $path;
    }

    /** @param Collection<int, \Spatie\Health\Support\Result> $checkResults */
    public function save(Collection $checkResults): void
    {
        $report = new Report(now());

        $checkResults
            ->map(function (Result $result) {
                return new Line(
                    name: $result->check->name(),
                    message: $result->getMessage(),
                    status: (string)$result->status->value,
                    meta: $result->meta,
                );
            })
            ->each(function (Line $line) use ($report) {
                $report->addLine($line);
            });

        $contents = $report->toJson();

        $this->disk->write($this->path, $contents);
    }

    public function latestResults(): ?Report
    {
        $content = null;

        try {
            $content = $this->disk->read($this->path);
        } catch (Exception $exception) {
            report($exception);
        }

        if (!$content) {
            return null;
        }

        return Report::fromJson($content);
    }
}
