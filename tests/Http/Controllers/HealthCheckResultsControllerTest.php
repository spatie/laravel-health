<?php

use Illuminate\Support\Facades\Route;
use Spatie\Health\Commands\RunChecksCommand;
use Spatie\Health\Facades\Health;
use Spatie\Health\Http\Controllers\HealthCheckResultsController;
use Spatie\Health\Tests\TestClasses\FakeUsedDiskSpaceCheck;
use function Pest\Laravel\artisan;
use function Pest\Laravel\getJson;
use function Spatie\PestPluginTestTime\testTime;
use function Spatie\Snapshots\assertMatchesJsonSnapshot;
use function Spatie\Snapshots\assertMatchesSnapshot;

beforeEach(function() {
    testTime()->freeze('2021-01-01 00:00:00');

    Route::get('/', HealthCheckResultsController::class);

    Health::checks([
        FakeUsedDiskSpaceCheck::new()->fakeDiskUsagePercentage(100)
    ]);

    artisan(RunChecksCommand::class);
});

it('will display the results as json when the request accepts json', function() {
    $json = getJson('/')
        ->assertSuccessful()
        ->json();

    assertMatchesSnapshot($json);
});
