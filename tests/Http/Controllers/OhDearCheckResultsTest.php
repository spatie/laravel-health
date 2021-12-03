<?php

use Illuminate\Support\Facades\Route;
use Spatie\Health\Http\Controllers\HealthCheckJsonResultsController;
use function Pest\Laravel\artisan;
use function Pest\Laravel\get;
use function Pest\Laravel\getJson;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\Facades\Health;
use Spatie\Health\Http\Controllers\HealthCheckResultsController;
use Spatie\Health\Tests\TestClasses\FakeUsedDiskSpaceCheck;
use function Spatie\PestPluginTestTime\testTime;
use function Spatie\Snapshots\assertMatchesSnapshot;

beforeEach(function () {
    testTime()->freeze('2021-01-01 00:00:00');

    $this->check = FakeUsedDiskSpaceCheck::new()->fakeDiskUsagePercentage(100);

    Health::checks([
        $this->check,
    ]);

    artisan(RunHealthChecksCommand::class);

    config()->set('health.oh_dear_endpoint', [
        'enabled' => true,
        'secret' => 'my-secret',
        'url' => 'my-url'
    ]);

    $this->refreshServiceProvider();
});

it('will display the results as json when the endpoint is enabled and the secret is correct', function () {
    $json = get('my-url', ['oh-dear-health-check-secret' => 'my-secret'])
        ->assertSuccessful()
        ->json();

    assertMatchesSnapshot($json);
});

it('will not display the results when the endpoint is disabled', function () {
    config()->set('health.oh_dear_endpoint', [
        'enabled' => false,
        'secret' => 'my-secret',
        'url' => 'my-other-url'
    ]);

    $this->refreshServiceProvider();

    get('my-other-url', ['oh-dear-health-check-secret' => 'my-secret'])->assertStatus(404);
});

it('will not display the results when the secret is wrong', function () {
    get('my-url', ['oh-dear-health-check-secret' => 'wrong-secret'])->assertStatus(403);
});

it('will not display the results when the secret is missing', function () {
    get('my-url')->assertStatus(403);
});
