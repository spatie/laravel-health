<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Queue;
use Spatie\Health\Checks\Checks\QueueCheck;
use Spatie\Health\Commands\DispatchQueueCheckJobsCommand;
use Spatie\Health\Enums\Status;
use Spatie\Health\Facades\Health;
use Spatie\Health\Jobs\HealthQueueJob;

use function Pest\Laravel\artisan;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertIsBool;
use function Spatie\PestPluginTestTime\testTime;
use function Spatie\Snapshots\assertMatchesObjectSnapshot;
use function Spatie\Snapshots\assertMatchesSnapshot;

beforeEach(function () {
    $this->queueCheck = QueueCheck::new();

    Health::checks([
        QueueCheck::new(),
    ]);

    testTime()->freeze('2024-01-01 00:00:00');
});

it('can check whether the queue jobs are still running', function () {
    artisan(DispatchQueueCheckJobsCommand::class)->assertSuccessful();

    $result = $this->queueCheck->run();
    expect($result->status)->toBe(Status::ok());

    testTime()->addMinutes(5);
    $result = $this->queueCheck->run();
    expect($result->status)->toBe(Status::ok());

    testTime()->addSecond();
    $result = $this->queueCheck->run();
    expect($result->status)->toBe(Status::failed());
})->skipOnOldCarbon();

it('can use custom max age of the heartbeat for queue jobs', function () {
    $this->queueCheck->failWhenHealthJobTakesLongerThanMinutes(10);

    artisan(DispatchQueueCheckJobsCommand::class)->assertSuccessful();

    $result = $this->queueCheck->run();
    expect($result->status)->toBe(Status::ok());

    testTime()->addMinutes(10);
    $result = $this->queueCheck->run();
    expect($result->status)->toBe(Status::ok());

    testTime()->addSecond();
    $result = $this->queueCheck->run();
    expect($result->status)->toBe(Status::failed());
})->skipOnOldCarbon();

it('will fail if only one queue is not working', function () {
    Health::clearChecks();

    $queueCheck = QueueCheck::new()->onQueue('payment');

    Health::checks([$queueCheck]);

    artisan(DispatchQueueCheckJobsCommand::class)->assertSuccessful();

    $result = $queueCheck->run();
    expect($result->status)->toBe(Status::ok());

    $queueCheck->onQueue(['payment', 'email']);
    $result = $queueCheck->run();
    expect($result->status)->toBe(Status::failed());
});

it('can specify on which queue check should be performed', function () {
    Queue::fake();

    Health::clearChecks();

    Health::checks([
        QueueCheck::new()->onQueue('email'),
    ]);

    artisan(DispatchQueueCheckJobsCommand::class)->assertSuccessful();

    Queue::assertPushedOn('email', HealthQueueJob::class);
});

it('can specify on which queues check should be performed', function () {
    Queue::fake();

    Health::clearChecks();

    Health::checks([
        QueueCheck::new()->onQueue(['email', 'payment']),
    ]);

    artisan(DispatchQueueCheckJobsCommand::class)->assertSuccessful();

    Queue::assertPushedOn('email', HealthQueueJob::class);
    Queue::assertPushedOn('payment', HealthQueueJob::class);
});

it('can get default queue settings', function () {
    // Set default queue connection
    $queueConnection = uniqid('connection');
    config()->set('queue.default', $queueConnection);

    // Set default queue name
    $queueName = uniqid('queue');
    config()->set("queue.connections.{$queueConnection}.queue", $queueName);

    expect($this->queueCheck->getQueues())->toBe([$queueName]);
});

it('will return warning on first failure when failAfterMinutes is set', function () {
    $check = QueueCheck::new()->failAfterMinutes(2);

    Health::clearChecks();
    Health::checks([$check]);

    $result = $check->run();

    expect($result->status)->toBe(Status::warning());
});

it('will return failed after grace period expires', function () {
    $check = QueueCheck::new()->failAfterMinutes(2);

    Health::clearChecks();
    Health::checks([$check]);

    // First failure - should be warning
    $result = $check->run();
    expect($result->status)->toBe(Status::warning());

    // Travel past the grace period
    Carbon::setTestNow(now()->addMinutes(3));

    // Second failure after grace period - should be failed
    $result = $check->run();
    expect($result->status)->toBe(Status::failed());

    Carbon::setTestNow();
});

it('will reset failure cache on successful queue run', function () {
    $check = QueueCheck::new()->failAfterMinutes(2);

    Health::clearChecks();
    Health::checks([$check]);

    // First: no heartbeat - should be warning
    $result = $check->run();
    expect($result->status)->toBe(Status::warning());

    // Dispatch heartbeat job (simulates queue running again after deploy)
    artisan(DispatchQueueCheckJobsCommand::class)->assertSuccessful();

    // Second: heartbeat present - should be ok (cache cleared)
    $result = $check->run();
    expect($result->status)->toBe(Status::ok());

    // Clear the heartbeat cache to simulate queue down again
    cache()->store($check->getCacheStoreName())->flush();

    // Third: failure again - should be warning (not failed, because cache was reset)
    $result = $check->run();
    expect($result->status)->toBe(Status::warning());
});

it('can serialize closures', function () {
    $check = QueueCheck::new()
        ->onQueue('sync')
        ->if(fn () => false);

    $result = serialize($check);
    // Replace with a consistent identifier
    $result = preg_replace('/s:32:"[0-9a-z]{32}";/', 's:32:"0000000000000000000000000000000000000000";', $result);

    assertMatchesSnapshot($result);
});

it('can serialize non closures', function () {
    $check = QueueCheck::new()
        ->onQueue('sync')
        ->if(true);

    $result = serialize($check);

    assertMatchesSnapshot($result);
});

it('can unserialize', function () {
    $check = QueueCheck::new()
        ->onQueue('sync')
        ->if(true)
        ->if(fn () => false);

    $result = unserialize(serialize($check));

    assertCount(2, $result->getRunConditions());
    assertIsBool($result->getRunConditions()[0]);
    assertInstanceOf(Closure::class, $result->getRunConditions()[1]);

    assertMatchesObjectSnapshot($result);
});
