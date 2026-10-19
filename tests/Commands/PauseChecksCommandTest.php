<?php

use Illuminate\Support\Facades\Cache;
use Spatie\Health\Commands\PauseHealthChecksCommand;

use function Pest\Laravel\artisan;
use function Spatie\PestPluginTestTime\testTime;

it('sets cache value to true for default ttl', function () {
    testTime()->freeze();

    artisan(PauseHealthChecksCommand::class)
        ->assertSuccessful()
        ->expectsOutputToContain('All health check paused until');

    expect(Cache::get(PauseHealthChecksCommand::CACHE_KEY))->toBeTrue();

    testTime()->addSeconds(PauseHealthChecksCommand::DEFAULT_TTL + 1);

    expect(Cache::get(PauseHealthChecksCommand::CACHE_KEY))->toBeNull();
});

it('sets cache value to true for custom ttl', function () {
    testTime()->freeze();

    artisan(PauseHealthChecksCommand::class, ['seconds' => '60'])
        ->assertSuccessful()
        ->expectsOutputToContain('All health check paused until');

    expect(Cache::get(PauseHealthChecksCommand::CACHE_KEY))->toBeTrue();

    testTime()->addSeconds(61);

    expect(Cache::get(PauseHealthChecksCommand::CACHE_KEY))->toBeNull();
});
