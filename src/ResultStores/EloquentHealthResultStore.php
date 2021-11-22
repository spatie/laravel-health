<?php

namespace Spatie\Health\ResultStores;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use OhDear\HealthCheckReport\Line;
use OhDear\HealthCheckReport\Report;
use Spatie\Health\Models\CheckResultHistoryItem;
use Spatie\Health\Support\Result;

class EloquentHealthResultStore implements ResultStore
{
    /** @param Collection<int, Result> $checkResults */
    public function save(Collection $checkResults): void
    {
        $batch = Str::uuid();

        $checkResults->each(function (Result $result) use ($batch) {
            CheckResultHistoryItem::create([
                'check_name' => $result->check->getName(),
                'status' => $result->status,
                'message' => $result->getMessage(),
                'meta' => $result->meta,
                'batch' => $batch,
                'ended_at' => $result->ended_at,
            ]);
        });
    }

    public function latestResults(): ?Report
    {
        if (! $latestItem = CheckResultHistoryItem::latest()->first()) {
            return null;
        }

        /** @var array<int, Line> $reportLines */
        $reportLines = CheckResultHistoryItem::query()
            ->where('batch', $latestItem->batch)
            ->get()
            ->map(function (CheckResultHistoryItem $historyItem) {
                return new Line(
                    name: $historyItem->check_name,
                    message: $historyItem->message,
                    status: $historyItem->status,
                    meta: $historyItem->meta,
                );
            })
            ->toArray();

        return new Report(
            finishedAt: $latestItem->created_at,
            lines: $reportLines,
        );
    }
}
