<?php

use Illuminate\Support\Facades\Storage;
use Spatie\Health\Models\CheckResultHistoryItem;
use Spatie\Health\ResultStores\EloquentHealthResultStore;
use Spatie\Health\ResultStores\ResultStore;
use function Pest\Laravel\artisan;
use Spatie\Health\Commands\RunChecksCommand;
use Spatie\Health\Facades\Health;
use Spatie\Health\ResultStores\JsonFileHeathResultStore;
use Spatie\Health\Tests\TestClasses\FakeDiskSpaceCheck;
use function Spatie\PestPluginTestTime\testTime;
use function Spatie\Snapshots\assertMatchesJsonSnapshot;

beforeEach(function() {
    testTime()->freeze('2021-01-01 00:00:00');

    config()->set('health.result_stores', [
        EloquentHealthResultStore::class
    ]);

    Health::checks([
        FakeDiskSpaceCheck::new(),
    ]);
});

it('can write check results to the database', function () {
    artisan(RunChecksCommand::class)->assertSuccessful();

    expect(CheckResultHistoryItem::get())->toHaveCount(1);
});

it('can retrieve the latest results from json', function() {
    $report = app(ResultStore::class)->latestResults();
    expect($report)->toBeNull();

    artisan(RunChecksCommand::class)->assertSuccessful();

    $report = app(ResultStore::class)->latestResults();

    assertMatchesJsonSnapshot($report->toJson());
});
