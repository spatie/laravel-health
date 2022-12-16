<?php

namespace Spatie\Health\Commands;

use Spatie\Health\Checks\Checks\ScheduleCheck;

class ScheduleCheckHeartbeatCommand extends HeartbeatCommand
{
    protected $signature = 'health:schedule-check-heartbeat';

    public function runHeartbeat(): int
    {
        $check = $this->getCheckInstance();

        cache()->store($check->getCacheStoreName())->set($check->getCacheKey(), now()->timestamp);

        return static::SUCCESS;
    }

    public function getCheckClass(): string
    {
        return ScheduleCheck::class;
    }
}
