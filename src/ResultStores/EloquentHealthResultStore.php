<?php

namespace Spatie\Health\ResultStores;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Health\Checks\Result;
use Spatie\Health\Models\HealthCheckResultHistoryItem;
use Spatie\Health\ResultStores\Report\Report;
use Spatie\Health\ResultStores\Report\ReportedCheck;

class EloquentHealthResultStore implements ResultStore
{
    /** @param Collection<int, Result> $checkResults */
    public function save(Collection $checkResults): void
    {
        $batch = Str::uuid();

        $checkResults->each(function (Result $result) use ($batch) {
            HealthCheckResultHistoryItem::create([
                'check_name' => $result->check->getName(),
                'status' => $result->status,
                'message' => $result->getMessage(),
                'meta' => $result->meta,
                'batch' => $batch,
                'ended_at' => $result->ended_at,
            ]);
        });
    }

    public function latestReport(): ?Report
    {
        if (! $latestItem = HealthCheckResultHistoryItem::latest()->first()) {
            return null;
        }

        /** @var Collection<int, ReportedCheck> $checkResults */
        $checkResults = HealthCheckResultHistoryItem::query()
            ->where('batch', $latestItem->batch)
            ->get()
            ->map(function (HealthCheckResultHistoryItem $historyItem) {
                return new ReportedCheck(
                    name: $historyItem->check_name,
                    message: $historyItem->message,
                    status: $historyItem->status,
                    meta: $historyItem->meta,
                );
            });

        return new Report(
            finishedAt: $latestItem->created_at,
            checkResults: $checkResults,
        );
    }
}
