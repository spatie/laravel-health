<?php

namespace Spatie\Health\ResultStores;

use DateTime;
use Illuminate\Support\Collection;
use Spatie\Health\Checks\Result;
use Spatie\Health\Facades\Health;
use Spatie\Health\ResultStores\StoredCheckResults\StoredCheckResult;
use Spatie\Health\ResultStores\StoredCheckResults\StoredCheckResults;

class CacheHealthResultStore implements ResultStore
{
    public function __construct(
        public string $store = 'file',
        public string $cacheKey = 'health:storeResults',
    ) {}

    public function save(Collection $checkResults): void
    {
        $report = new StoredCheckResults(now());
        $serverKey = \Spatie\Health\Facades\Health::getServerKey();

        $checkResults
            ->map(function (Result $result) use ($serverKey) {
                return new StoredCheckResult(
                    serverKey: $serverKey,
                    name: $result->check->getName(),
                    label: $result->check->getLabel(),
                    notificationMessage: $result->getNotificationMessage(),
                    shortSummary: $result->getShortSummary(),
                    status: (string) $result->status->value,
                    meta: $result->meta,
                );
            })
            ->each(function (StoredCheckResult $check) use ($report) {
                $report->addCheck($check);
            });

        $currentData = cache()
            ->store($this->store)
            ->get($this->cacheKey);

        $currentData[$serverKey] = $report->toJson();

        cache()
            ->store($this->store)
            ->put($this->cacheKey, $currentData);
    }

    public function latestResults($onlySameServerKey = true): ?StoredCheckResults
    {
        $healthResults = cache()
            ->store($this->store)
            ->get($this->cacheKey);

        if (! $healthResults) {
            return [];
        }

        if ($onlySameServerKey) {
            $serverKey = Health::getServerKey();
            $healthResults = [$serverKey => $healthResults[$serverKey] ?? '{}'];
        }

        $result = [];
        $maxFinished = 0;
        foreach ($healthResults as $checkJson) {
            $storedCheckResultsData = json_decode($checkJson, true);

            $maxFinished = max($storedCheckResultsData['finishedAt'], $maxFinished);
            foreach ($storedCheckResultsData['checkResults'] as $checkResult) {
                $result[] = new StoredCheckResult(
                    serverKey: $checkResult['serverKey'],
                    name: $checkResult['name'],
                    label: $checkResult['label'],
                    notificationMessage: $checkResult['notificationMessage'],
                    shortSummary: $checkResult['shortSummary'],
                    status: $checkResult['status'],
                    meta: $checkResult['meta'],
                );
            }
        }

        return new StoredCheckResults(
            finishedAt: (new DateTime)->setTimestamp($maxFinished),
            checkResults: collect($result),
        );
    }
}
