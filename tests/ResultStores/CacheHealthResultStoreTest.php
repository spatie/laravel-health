<?php

use function Pest\Laravel\artisan;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\Facades\Health;
use Spatie\Health\ResultStores\CacheHealthResultStore;
use Spatie\Health\ResultStores\ResultStore;
use Spatie\Health\Tests\TestClasses\FakeUsedDiskSpaceCheck;
use function Spatie\PestPluginTestTime\testTime;
use function Spatie\Snapshots\assertMatchesJsonSnapshot;

beforeEach(function () {
    testTime()->freeze('2021-01-01 00:00:00');

    config()->set('health.result_stores', [
        CacheHealthResultStore::class,
    ]);

    Health::checks([
        FakeUsedDiskSpaceCheck::new(),
    ]);

    cache()->store('file')->clear();
});

it('can cache check results', function () {
    artisan(RunHealthChecksCommand::class)->assertSuccessful();

    $json = cache()->store('file')->get('health:storeResults');

    assertMatchesJsonSnapshot($json);
});

it('can retrieve the latest results from cache', function () {
    $report = app(ResultStore::class)->latestResults();
    expect($report)->toBeNull();

    artisan(RunHealthChecksCommand::class)->assertSuccessful();

    $report = app(ResultStore::class)->latestResults();

    assertMatchesJsonSnapshot($report->toJson());
});
