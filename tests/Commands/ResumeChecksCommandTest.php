<?php

use Illuminate\Support\Facades\Cache;
use Spatie\Health\Commands\PauseHealthChecksCommand;
use Spatie\Health\Commands\ResumeHealthChecksCommand;

use function Pest\Laravel\artisan;

it('forgets cache value', function () {
    Cache::put(PauseHealthChecksCommand::CACHE_KEY, true);

    artisan(ResumeHealthChecksCommand::class)
        ->assertSuccessful()
        ->expectsOutput('All health check resumed');

    expect(Cache::has(PauseHealthChecksCommand::CACHE_KEY))->toBeFalse();
});
