<?php

use Illuminate\Support\Facades\Storage;
use Spatie\Health\Commands\RunChecksCommand;
use Spatie\Health\Facades\Health;
use Spatie\Health\ResultStores\JsonFileHeathResultStore;
use Spatie\Health\Tests\TestClasses\FakeDiskSpaceCheck;
use function Pest\Laravel\artisan;
use function Spatie\PestPluginTestTime\testTime;
use function Spatie\Snapshots\assertMatchesSnapshot;

it('can write check results to a json file', function() {
    testTime()->freeze('2021-01-01 00:00:00');

    Storage::fake('s3');

    $path = 'health.json';

    config()->set('health.result_stores', [
        JsonFileHeathResultStore::class => [
            'diskName' => 's3',
            'path' => $path,
        ],
    ]);

    Health::checks([
        FakeDiskSpaceCheck::new(),
    ]);

    artisan(RunChecksCommand::class)->assertSuccessful();

    Storage::disk('s3')->assertExists($path);

    $content = Storage::disk('s3')->get($path);

    assertMatchesSnapshot($content);
});
