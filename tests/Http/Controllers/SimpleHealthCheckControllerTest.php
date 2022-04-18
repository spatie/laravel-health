<?php

use function Pest\Laravel\artisan;
use function Pest\Laravel\getJson;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\Facades\Health;
use Spatie\Health\Http\Controllers\SimpleHealthCheckController;
use Spatie\Health\Tests\TestClasses\FakeRedisCheck;
use function Spatie\PestPluginTestTime\testTime;
use function Spatie\Snapshots\assertMatchesSnapshot;
use Symfony\Component\HttpFoundation\Response;

beforeEach(function () {
    testTime()->freeze('2021-01-01 00:00:00');

    Route::get('/', SimpleHealthCheckController::class);

    $this->check = FakeRedisCheck::new()->replyWith(fn () => true);

    Health::checks([
        $this->check,
    ]);
});

it('will return a 200 status for a healthy check', function () {
    artisan(RunHealthChecksCommand::class);

    $json = getJson('/')
        ->assertOk()
        ->json();

    assertMatchesSnapshot($json);
});

it('will return a 503 status for a unhealthy check', function () {
    $this->check->replyWith(fn () => false);

    artisan(RunHealthChecksCommand::class);

    $json = getJson('/')
        ->assertStatus(Response::HTTP_SERVICE_UNAVAILABLE)
        ->json();

    assertMatchesSnapshot($json);
});
