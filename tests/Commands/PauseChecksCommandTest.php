<?php

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;
use Spatie\Health\Commands\PauseHealthChecksCommand;

use function Pest\Laravel\artisan;

it('sets cache value to true for default ttl', function () {
    $mockRepository = Mockery::mock(Repository::class);

    $mockRepository->shouldReceive('put')
        ->once()
        ->with(
            PauseHealthChecksCommand::CACHE_KEY,
            true,
            PauseHealthChecksCommand::DEFAULT_TTL
        )
        ->andReturn(true);

    Cache::swap($mockRepository);

    Cache::shouldReceive('driver')->andReturn($mockRepository);

    artisan(PauseHealthChecksCommand::class)
        ->assertSuccessful()
        ->expectsOutputToContain('All health check paused until');
});

it('sets cache value to true for custom ttl', function () {
    $mockRepository = Mockery::mock(Repository::class);

    $mockRepository->shouldReceive('put')
        ->once()
        ->with(
            PauseHealthChecksCommand::CACHE_KEY,
            true,
            60
        )
        ->andReturn(true);

    Cache::swap($mockRepository);

    Cache::shouldReceive('driver')->andReturn($mockRepository);

    artisan(PauseHealthChecksCommand::class, ['seconds' => '60'])
        ->assertSuccessful()
        ->expectsOutputToContain('All health check paused until');
});
