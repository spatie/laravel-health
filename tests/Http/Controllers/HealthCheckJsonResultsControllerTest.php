<?php

use Illuminate\Support\Facades\Route;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\Facades\Health;
use Spatie\Health\Http\Controllers\HealthCheckJsonResultsController;
use Spatie\Health\ResultStores\StoredCheckResults\StoredCheckResults;
use Spatie\Health\Tests\TestClasses\FakeUsedDiskSpaceCheck;

use function Pest\Laravel\artisan;
use function Pest\Laravel\getJson;
use function Spatie\PestPluginTestTime\testTime;
use function Spatie\Snapshots\assertMatchesSnapshot;

beforeEach(function () {
    testTime()->freeze('2021-01-01 00:00:00');

    Route::get('/', HealthCheckJsonResultsController::class);

    $this->check = FakeUsedDiskSpaceCheck::new()->fakeDiskUsagePercentage(100);

    Health::checks([
        $this->check,
    ]);
});

it('will display the results as json when the request accepts json', function () {
    artisan(RunHealthChecksCommand::class);

    $json = getJson('/')
        ->assertSuccessful()
        ->json();

    assertMatchesSnapshot($json);
});

it('the output of the json endpoint can be used to create a StoredCheckResults object', function () {
    artisan(RunHealthChecksCommand::class);

    $jsonString = getJson('/')
        ->assertSuccessful()
        ->content();

    $storedCheckResults = StoredCheckResults::fromJson($jsonString);

    expect($storedCheckResults)->toBeInstanceOf(StoredCheckResults::class);
});

it('will run the checks when the run get parameter is passed and return the results as json', function () {
    $jsonString = getJson('/?fresh')
        ->assertSuccessful()
        ->content();

    $storedCheckResults = StoredCheckResults::fromJson($jsonString);

    expect($storedCheckResults)->toBeInstanceOf(StoredCheckResults::class)
        ->and($storedCheckResults->storedCheckResults)->toHaveCount(1);
});
