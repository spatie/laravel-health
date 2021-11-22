<?php

use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\artisan;
use Spatie\Health\Commands\RunChecksCommand;
use Spatie\Health\Facades\Health;
use Spatie\Health\ResultStores\JsonFileHealthResultStore;
use Spatie\Health\ResultStores\ResultStore;
use Spatie\Health\Tests\TestClasses\FakeDiskSpaceCheck;
use function Spatie\PestPluginTestTime\testTime;
use function Spatie\Snapshots\assertMatchesJsonSnapshot;

beforeEach(function () {
    $this->path = 'health.json';

    testTime()->freeze('2021-01-01 00:00:00');

    Storage::fake('s3');

    config()->set('health.result_stores', [
        JsonFileHealthResultStore::class => [
            'diskName' => 's3',
            'path' => $this->path,
        ],
    ]);

    Health::checks([
        FakeDiskSpaceCheck::new(),
    ]);
});

it('can write check results to a json file', function () {
    artisan(RunChecksCommand::class)->assertSuccessful();

    Storage::disk('s3')->assertExists($this->path);

    $content = Storage::disk('s3')->get($this->path);

    assertMatchesJsonSnapshot($content);
});

it('can retrieve the latest results from json', function () {
    $report = app(ResultStore::class)->latestReport();
    expect($report)->toBeNull();

    artisan(RunChecksCommand::class)->assertSuccessful();

    $report = app(ResultStore::class)->latestReport();

    assertMatchesJsonSnapshot($report->toJson());
});
