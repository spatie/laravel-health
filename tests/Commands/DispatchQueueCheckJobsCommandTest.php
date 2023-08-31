<?php

use Illuminate\Support\Facades\Bus;
use Spatie\Health\Checks\Checks\QueueCheck;
use Spatie\Health\Commands\DispatchQueueCheckJobsCommand;
use Spatie\Health\Facades\Health;
use Spatie\Health\Jobs\HealthQueueJob;

use function Pest\Laravel\artisan;

it('dispatch to default queue by default', function () {
    Bus::fake();

    Health::checks([
        QueueCheck::new()->new(),
    ]);

    artisan(DispatchQueueCheckJobsCommand::class)->assertSuccessful();

    Bus::assertDispatchedTimes(HealthQueueJob::class, 1);
    Bus::assertDispatched(fn (HealthQueueJob $job) => $job->queue === 'default');
});

it('dispatch to specified queue', function () {
    Bus::fake();

    Health::checks([
        QueueCheck::new()->onQueue('queue'),
    ]);

    artisan(DispatchQueueCheckJobsCommand::class)->assertSuccessful();

    Bus::assertDispatchedTimes(HealthQueueJob::class, 1);
    Bus::assertDispatched(fn (HealthQueueJob $job) => $job->queue === 'queue');
});

it('dispatch to multiple queues of single check', function () {
    Bus::fake();

    Health::checks([
        QueueCheck::new()->onQueue(['queue1', 'queue2']),
    ]);

    artisan(DispatchQueueCheckJobsCommand::class)->assertSuccessful();

    Bus::assertDispatchedTimes(HealthQueueJob::class, 2);
    Bus::assertDispatched(fn (HealthQueueJob $job) => $job->queue === 'queue1');
    Bus::assertDispatched(fn (HealthQueueJob $job) => $job->queue === 'queue2');
});

it('dispatch multiple checks', function () {
    Bus::fake();

    Health::checks([
        QueueCheck::new()->onQueue('queue1')->name('Queue 1'),
        QueueCheck::new()->onQueue('queue2')->name('Queue 2'),
    ]);

    artisan(DispatchQueueCheckJobsCommand::class)->assertSuccessful();

    Bus::assertDispatchedTimes(HealthQueueJob::class, 2);
    Bus::assertDispatched(fn (HealthQueueJob $job) => $job->queue === 'queue1');
    Bus::assertDispatched(fn (HealthQueueJob $job) => $job->queue === 'queue2');
});

it('dispatch to multiple queues of multiple checks', function () {
    Bus::fake();

    Health::checks([
        QueueCheck::new()->onQueue(['queue1', 'queue2'])->name('Queue 12'),
        QueueCheck::new()->onQueue(['queue3', 'queue4'])->name('Queue 34'),
    ]);

    artisan(DispatchQueueCheckJobsCommand::class)->assertSuccessful();

    Bus::assertDispatchedTimes(HealthQueueJob::class, 4);
    Bus::assertDispatched(fn (HealthQueueJob $job) => $job->queue === 'queue1');
    Bus::assertDispatched(fn (HealthQueueJob $job) => $job->queue === 'queue2');
    Bus::assertDispatched(fn (HealthQueueJob $job) => $job->queue === 'queue3');
    Bus::assertDispatched(fn (HealthQueueJob $job) => $job->queue === 'queue4');
});
