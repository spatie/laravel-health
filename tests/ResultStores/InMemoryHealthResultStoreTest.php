<?php

use Spatie\Health\Checks\Result;
use Spatie\Health\ResultStores\InMemoryHealthResultStore;
use Spatie\Health\ResultStores\StoredCheckResults\StoredCheckResults;
use Spatie\Health\Tests\TestClasses\FakeUsedDiskSpaceCheck;

it('can keep results in memory', function () {
    $store = new InMemoryHealthResultStore();

    $check = FakeUsedDiskSpaceCheck::new();

    $checkResults = collect([Result::make('disk')->check($check)]);

    $store->save($checkResults);

    expect($store->latestResults())->toBeInstanceOf(StoredCheckResults::class)
        ->and($store->latestResults()->storedCheckResults)->toHaveCount(1);
});
