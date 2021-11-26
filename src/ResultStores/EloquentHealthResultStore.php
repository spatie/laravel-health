<?php

namespace Spatie\Health\ResultStores;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Health\Checks\Result;
use Spatie\Health\Models\HealthCheckResultHistoryItem;
use Spatie\Health\ResultStores\StoredCheckResults\StoredCheckResult;
use Spatie\Health\ResultStores\StoredCheckResults\StoredCheckResults;

class EloquentHealthResultStore implements ResultStore
{
    /** @param Collection<int, Result> $checkResults */
    public function save(Collection $checkResults): void
    {
        $batch = Str::uuid();

        $checkResults->each(function (Result $result) use ($batch) {
            HealthCheckResultHistoryItem::create([
                'check_name' => $result->check->getName(),
                'check_label' => $result->check->getLabel(),
                'status' => $result->status,
                'notification_message' => $result->getNotificationMessage(),
                'short_summary' => $result->getShortSummary(),
                'meta' => $result->meta,
                'batch' => $batch,
                'ended_at' => $result->ended_at,
            ]);
        });
    }

    public function latestResults(): ?StoredCheckResults
    {
        if (! $latestItem = HealthCheckResultHistoryItem::latest()->first()) {
            return null;
        }

        /** @var Collection<int, StoredCheckResult> $storedCheckResults */
        $storedCheckResults = HealthCheckResultHistoryItem::query()
            ->where('batch', $latestItem->batch)
            ->get()
            ->map(function (HealthCheckResultHistoryItem $historyItem) {
                return new StoredCheckResult(
                    name: $historyItem->check_name,
                    label: $historyItem->check_label,
                    notificationMessage: $historyItem->notification_message,
                    status: $historyItem->status,
                    meta: $historyItem->meta,
                );
            });

        return new StoredCheckResults(
            finishedAt: $latestItem->created_at,
            checkResults: $storedCheckResults,
        );
    }
}
