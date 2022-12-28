<?php

use Illuminate\Support\Facades\Queue;
use function Pest\Laravel\artisan;
use Spatie\Health\Checks\Checks\QueueCheck;
use Spatie\Health\Commands\QueueCheckHeartbeatCommand;
use Spatie\Health\Enums\Status;
use Spatie\Health\Facades\Health;
use Spatie\Health\Jobs\HealthQueueJob;
use function Spatie\PestPluginTestTime\testTime;

beforeEach(function () {
    $this->queueCheck = QueueCheck::new();

    Health::checks([
        QueueCheck::new(),
    ]);

    testTime()->freeze();
});

it('can check whether the queue jobs are still running', function () {
    artisan(QueueCheckHeartbeatCommand::class)->assertSuccessful();

    $result = $this->queueCheck->run();
    expect($result->status)->toBe(Status::ok());

    testTime()->addMinutes(5)->subSecond();
    $result = $this->queueCheck->run();
    expect($result->status)->toBe(Status::ok());

    testTime()->addSecond();
    $result = $this->queueCheck->run();
    expect($result->status)->toBe(Status::failed());
});

it('can use custom max age of the heartbeat for queue jobs', function () {
    $this->queueCheck->heartbeatMaxAgeInMinutes(10);

    artisan(QueueCheckHeartbeatCommand::class)->assertSuccessful();

    $result = $this->queueCheck->run();
    expect($result->status)->toBe(Status::ok());

    testTime()->addMinutes(10)->subSecond();
    $result = $this->queueCheck->run();
    expect($result->status)->toBe(Status::ok());

    testTime()->addSecond();
    $result = $this->queueCheck->run();
    expect($result->status)->toBe(Status::failed());
});

it('will fail if only one queue is not working', function () {
    Health::clearChecks();

    $queueCheck = QueueCheck::new()->onQueue('payment');

    Health::checks([$queueCheck]);

    artisan(QueueCheckHeartbeatCommand::class)->assertSuccessful();

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

    artisan(QueueCheckHeartbeatCommand::class)->assertSuccessful();

    Queue::assertPushedOn('email', HealthQueueJob::class);
});

it('can specify on which queues check should be performed', function () {
    Queue::fake();

    Health::clearChecks();

    Health::checks([
        QueueCheck::new()->onQueue(['email', 'payment']),
    ]);

    artisan(QueueCheckHeartbeatCommand::class)->assertSuccessful();

    Queue::assertPushedOn('email', HealthQueueJob::class);
    Queue::assertPushedOn('payment', HealthQueueJob::class);
});
