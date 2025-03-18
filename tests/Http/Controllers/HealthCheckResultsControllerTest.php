<?php

use Illuminate\Support\Facades\Route;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\Facades\Health;
use Spatie\Health\Http\Controllers\HealthCheckResultsController;
use Spatie\Health\Tests\TestClasses\FakeUsedDiskSpaceCheck;

use function Pest\Laravel\artisan;
use function Pest\Laravel\get;
use function Spatie\PestPluginTestTime\testTime;

beforeEach(function () {
    testTime()->freeze('2021-01-01 00:00:00');

    Route::get('/', HealthCheckResultsController::class);

    $this->check = FakeUsedDiskSpaceCheck::new()->fakeDiskUsagePercentage(100);

    Health::checks([
        $this->check,
    ]);
});

it('can display the results as html', function () {
    artisan(RunHealthChecksCommand::class);

    get('/')
        ->assertSuccessful()
        ->assertViewIs('health::list')
        ->assertSee($this->check->getLabel());
});

it('will run the checks when the run get parameter is passed and return the results as json', function () {
    get('/?fresh')
        ->assertSuccessful()
        ->assertViewIs('health::list')
        ->assertSee($this->check->getLabel());
});
