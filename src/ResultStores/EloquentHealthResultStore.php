<?php

namespace Spatie\Health\ResultStores;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Health\Checks\Result;
use Spatie\Health\Exceptions\CouldNotSaveResultsInStore;
use Spatie\Health\Facades\Health;
use Spatie\Health\Models\HealthCheckResultHistoryItem;
use Spatie\Health\ResultStores\StoredCheckResults\StoredCheckResult;
use Spatie\Health\ResultStores\StoredCheckResults\StoredCheckResults;

class EloquentHealthResultStore implements ResultStore
{
    public static function determineHistoryItemModel(): string
    {
        $defaultHistoryClass = HealthCheckResultHistoryItem::class;
        $eloquentResultStore = EloquentHealthResultStore::class;

        $historyItemModel = config("health.result_stores.{$eloquentResultStore}.model", $defaultHistoryClass);

        if (! is_a($historyItemModel, $defaultHistoryClass, true)) {
            throw CouldNotSaveResultsInStore::doesNotExtendHealthCheckResultHistoryItem($historyItemModel);
        }

        return $historyItemModel;
    }

    /** @return HealthCheckResultHistoryItem|object */
    public static function getHistoryItemInstance()
    {
        $historyItemClassName = static::determineHistoryItemModel();

        return new $historyItemClassName();
    }

    /** @param  Collection<int, Result>  $checkResults */
    public function save(Collection $checkResults): void
    {
        $serverKey = \Spatie\Health\Facades\Health::getServerKey();

        $batch = Str::uuid();
        $checkResults->each(function (Result $result) use ($batch, $serverKey) {
            (static::determineHistoryItemModel())::create([
                'server_key' => $serverKey,
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

    public function latestResults($onlySameServerKey = true): ?StoredCheckResults
    {
        /** @var Model $modelInstance */
        $modelInstance = new (static::determineHistoryItemModel());
        if (!$latestItem = $modelInstance->newQuery()->latest()->first()) {
            return null;
        }

        $serverKey = Health::getServerKey();
        \PMLog::debug("[EloquentHealthResultStore][latestResults] Going to get {$serverKey} latest result");

        $latestChecksForServerKey = $modelInstance->newQuery()
            ->select(DB::raw("MAX(id) as max_id"), "server_key")
            ->when($onlySameServerKey, function (Builder $q) use ($serverKey) {
                $q->where("server_key", $serverKey);
            })
            ->groupBy("server_key")
            ->get()
            ->pluck("max_id", "server_key");

        $latestBatches = $modelInstance->newQuery()
            ->whereKey($latestChecksForServerKey)
            ->pluck("batch");

        /** @var Collection<int, StoredCheckResult> $storedCheckResults */
        $storedCheckResults = $modelInstance->newQuery()
            ->whereIn('batch', $latestBatches)
            ->get()
            ->map(function (HealthCheckResultHistoryItem $historyItem) {
                return new StoredCheckResult(
                    batch: $historyItem->batch,
                    serverKey: $historyItem->server_key,
                    name: $historyItem->check_name,
                    label: $historyItem->check_label,
                    notificationMessage: $historyItem->notification_message,
                    shortSummary: $historyItem->short_summary,
                    status: $historyItem->status,
                    meta: $historyItem->meta,
                );
            });
        \PMLog::debug("[EloquentHealthResultStore][latestResults] Got {$storedCheckResults->count()} raws");

        return new StoredCheckResults(
            finishedAt: $latestItem->created_at,
            checkResults: $storedCheckResults,
        );
    }
}
