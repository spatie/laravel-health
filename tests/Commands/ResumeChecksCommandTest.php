<?php

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;
use Spatie\Health\Commands\PauseHealthChecksCommand;
use Spatie\Health\Commands\ResumeHealthChecksCommand;

use function Pest\Laravel\artisan;

it('forgets cache value', function () {
    $mockRepository = Mockery::mock(Repository::class);

    $mockRepository->shouldReceive('forget')
        ->once()
        ->with(PauseHealthChecksCommand::CACHE_KEY)
        ->andReturn(true);

    Cache::swap($mockRepository);

    Cache::shouldReceive('driver')->andReturn($mockRepository);

    artisan(ResumeHealthChecksCommand::class)
        ->assertSuccessful()
        ->expectsOutput('All health check resumed');
});
