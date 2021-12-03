<?php

use Illuminate\Support\Facades\Route;
use function Pest\Laravel\artisan;
use function Pest\Laravel\get;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\Facades\Health;
use Spatie\Health\Http\Controllers\HealthCheckResultsController;
use Spatie\Health\Tests\TestClasses\FakeUsedDiskSpaceCheck;
use function Spatie\PestPluginTestTime\testTime;

beforeEach(function () {
    testTime()->freeze('2021-01-01 00:00:00');

    Route::get('/', HealthCheckResultsController::class);

    $this->check = FakeUsedDiskSpaceCheck::new()->fakeDiskUsagePercentage(100);

    Health::checks([
        $this->check,
    ]);

    artisan(RunHealthChecksCommand::class);
});

it('can display the results as html', function () {
    get('/')
        ->assertSuccessful()
        ->assertViewIs('health::list')
        ->assertSee($this->check->getLabel());
});
