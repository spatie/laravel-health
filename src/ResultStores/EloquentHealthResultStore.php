<?php

namespace Spatie\Health\ResultStores;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Health\Exceptions\CouldNotSaveResultsInStore;
use Spatie\Health\Checks\Result;
use Spatie\Health\Models\HealthCheckResultHistoryItem;
use Spatie\Health\ResultStores\StoredCheckResults\StoredCheckResult;
use Spatie\Health\ResultStores\StoredCheckResults\StoredCheckResults;

class EloquentHealthResultStore implements ResultStore
{
    public static function determineHistoryItemModel(): string
    {
        $historyItemModel = config(
            'health.result_stores.' . EloquentHealthResultStore::class . '.model',
            HealthCheckResultHistoryItem::class,
        );

        if (! is_a($historyItemModel, HealthCheckResultHistoryItem::class, true)) {
            throw CouldNotSaveResultsInStore::doesNotExtendHealthCheckResultHistoryItem($historyItemModel);
        }

        return $historyItemModel;
    }

    public static function getHistoryItemInstance(): HealthCheckResultHistoryItem
    {
        $historyItemClassName = self::determineHistoryItemModel();

        return new $historyItemClassName();
    }

    /** @param Collection<int, Result> $checkResults */
    public function save(Collection $checkResults): void
    {
        $batch = Str::uuid();
        $checkResults->each(function (Result $result) use ($batch) {
            (self::determineHistoryItemModel())::create([
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
        if (! $latestItem = (self::determineHistoryItemModel())::latest()->first()) {
            return null;
        }

        /** @var Collection<int, StoredCheckResult> $storedCheckResults */
        $storedCheckResults = (self::determineHistoryItemModel())::query()
            ->where('batch', $latestItem->batch)
            ->get()
            ->map(function (HealthCheckResultHistoryItem $historyItem) {
                return new StoredCheckResult(
                    name: $historyItem->check_name,
                    label: $historyItem->check_label,
                    notificationMessage: $historyItem->notification_message,
                    shortSummary: $historyItem->short_summary,
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
