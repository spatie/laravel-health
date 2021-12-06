<?php

use Spatie\Health\ResultStores\InMemoryHealthResultStore;
use Spatie\Health\ResultStores\StoredCheckResults\StoredCheckResult;
use Spatie\Health\ResultStores\StoredCheckResults\StoredCheckResults;

it('can keep results in memory', function () {
    $store = new InMemoryHealthResultStore();

    $checkResults = collect([StoredCheckResult::make('disk')]);

    $store->save($checkResults);

    expect($store->latestResults())->toBeInstanceOf(StoredCheckResults::class)
        ->and($store->latestResults()->storedCheckResults)->toHaveCount(1);
});
