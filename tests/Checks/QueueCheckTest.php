<?php

use Spatie\Health\Facades\Health;
use function Spatie\PestPluginTestTime\testTime;
use Spatie\Health\Checks\Checks\QueueCheck;
use function Pest\Laravel\artisan;
use Spatie\Health\Commands\QueueCheckHeartbeatCommand;
use Spatie\Health\Enums\Status;

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
