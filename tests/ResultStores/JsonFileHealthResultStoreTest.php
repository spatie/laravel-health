<?php

use Illuminate\Support\Facades\Storage;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\Enums\Status;
use Spatie\Health\Facades\Health;
use Spatie\Health\ResultStores\JsonFileHealthResultStore;
use Spatie\Health\ResultStores\ResultStore;
use Spatie\Health\Tests\TestClasses\FakeUsedDiskSpaceCheck;

use function Pest\Laravel\artisan;
use function Spatie\PestPluginTestTime\testTime;
use function Spatie\Snapshots\assertMatchesJsonSnapshot;

beforeEach(function () {
    $this->path = 'health.json';

    testTime()->freeze('2021-01-01 00:00:00');

    Storage::fake('s3');

    config()->set('health.result_stores', [
        JsonFileHealthResultStore::class => [
            'disk' => 's3',
            'path' => $this->path,
        ],
    ]);

    $this->fakeDiskSpaceCheck = FakeUsedDiskSpaceCheck::new();

    Health::checks([
        $this->fakeDiskSpaceCheck,
    ]);
});

it('can write check results to a json file', function () {
    artisan(RunHealthChecksCommand::class)->assertSuccessful();

    Storage::disk('s3')->assertExists($this->path);

    $content = Storage::disk('s3')->get($this->path);

    assertMatchesJsonSnapshot($content);
});

it('can retrieve the latest results from json', function () {
    $report = app(ResultStore::class)->latestResults();
    expect($report)->toBeNull();

    artisan(RunHealthChecksCommand::class)->assertSuccessful();

    $report = app(ResultStore::class)->latestResults();

    assertMatchesJsonSnapshot($report->toJson());
});

it('can write skipped results to a json file', function () {
    $this
        ->fakeDiskSpaceCheck
        ->everyFiveMinutes();

    artisan(RunHealthChecksCommand::class)->assertSuccessful();

    testTime()->addMinutes(4);

    artisan(RunHealthChecksCommand::class)->assertSuccessful();

    $report = app(ResultStore::class)->latestResults();

    expect($report->containsCheckWithStatus(Status::skipped()))->toBeTrue();
});
