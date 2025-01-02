<?php

use Illuminate\Contracts\Cache\Repository;
use Spatie\Health\Commands\PauseHealthChecksCommand;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\Facades\Health;
use Spatie\Health\Http\Controllers\SimpleHealthCheckController;
use Spatie\Health\Tests\TestClasses\FakeRedisCheck;
use Symfony\Component\HttpFoundation\Response;

use function Pest\Laravel\artisan;
use function Pest\Laravel\getJson;
use function Spatie\PestPluginTestTime\testTime;
use function Spatie\Snapshots\assertMatchesSnapshot;

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

it('does not perform checks if checks are paused', function () {
    artisan(RunHealthChecksCommand::class);

    $mockRepository = Mockery::mock(Repository::class);

    $mockRepository->shouldReceive('missing')
        ->once()
        ->with(PauseHealthChecksCommand::CACHE_KEY)
        ->andReturn(false);

    Cache::swap($mockRepository);

    Cache::shouldReceive('driver')->andReturn($mockRepository);

    // If the RunHealthChecksCommand were called (instead of being skipped as expected),
    // the test should fail with the error similar to:
    // "Received Mockery_2_Illuminate_Contracts_Cache_Repository::get(), but no expectations were specified."
    $json = getJson('/')
        ->assertOk()
        ->json();

    assertMatchesSnapshot($json);
});
