<?php

use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\Facades\Health;
use Spatie\Health\ResultStores\ResultStore;
use Spatie\Health\ResultStores\StoredCheckResults\StoredCheckResults;
use Spatie\Health\Tests\TestClasses\FakeUsedDiskSpaceCheck;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\artisan;
use function Spatie\PestPluginTestTime\testTime;
use function Spatie\Snapshots\assertMatchesJsonSnapshot;

beforeEach(function () {
    testTime()->freeze('2021-01-01 00:00:00');

    config()->set('health.result_stores.default', 'memory,json');
    $this->path = 'health.json';

    testTime()->freeze('2021-01-01 00:00:00');

    Storage::fake('s3');
    config()->set('health.result_stores.stores.json.disk','s3');
    config()->set('health.result_stores.stores.json.path',$this->path);

    $this->fakeDiskSpaceCheck = FakeUsedDiskSpaceCheck::new();

    Health::checks([
        $this->fakeDiskSpaceCheck,
    ]);
});

it('can keep results in memory and can write check results to a json file', function () {
    // Memory Test
    artisan(RunHealthChecksCommand::class)->assertSuccessful();

    $report = app(ResultStore::class)->latestResults();

    expect($report)->toBeInstanceOf(StoredCheckResults::class);
    expect($report->storedCheckResults)->toHaveCount(1);
    // Json Test
    artisan(RunHealthChecksCommand::class)->assertSuccessful();

    Storage::disk('s3')->assertExists($this->path);

    $content = Storage::disk('s3')->get($this->path);

    assertMatchesJsonSnapshot($content);
});
