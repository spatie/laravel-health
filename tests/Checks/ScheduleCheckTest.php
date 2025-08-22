<?php

use Spatie\Health\Checks\Checks\ScheduleCheck;
use Spatie\Health\Commands\ScheduleCheckHeartbeatCommand;
use Spatie\Health\Enums\Status;
use Spatie\Health\Facades\Health;

use function Pest\Laravel\artisan;
use function Spatie\PestPluginTestTime\testTime;

beforeEach(function () {
    $this->scheduleCheck = ScheduleCheck::new();

    Health::checks([
        ScheduleCheck::new(),
    ]);

    testTime()->freeze();
});

it('can check whether the scheduler is still running', function () {
    artisan(ScheduleCheckHeartbeatCommand::class)->assertSuccessful();

    $result = $this->scheduleCheck->run();
    expect($result->status)->toBe(Status::ok());

    testTime()->addMinute()->subSecond();
    $result = $this->scheduleCheck->run();
    expect($result->status)->toBe(Status::ok());

    testTime()->addSecond();
    $result = $this->scheduleCheck->run();
    expect($result->status)->toBe(Status::failed());
});

it('can use custom max age of the heartbeat', function () {
    $this->scheduleCheck->heartbeatMaxAgeInMinutes(2);

    artisan(ScheduleCheckHeartbeatCommand::class)->assertSuccessful();

    $result = $this->scheduleCheck->run();
    expect($result->status)->toBe(Status::ok());

    testTime()->addMinutes(2)->subSecond();
    $result = $this->scheduleCheck->run();
    expect($result->status)->toBe(Status::ok());

    testTime()->addSecond();
    $result = $this->scheduleCheck->run();
    expect($result->status)->toBe(Status::failed());
});
