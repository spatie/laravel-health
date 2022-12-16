<?php

namespace Spatie\Health\Commands;

use Spatie\Health\Checks\Checks\QueueCheck;
use Spatie\Health\Jobs\HealthQueueJob;

class QueueCheckHeartbeatCommand extends HeartbeatCommand
{
    protected $signature = 'health:queue-check-heartbeat';

    public function runHeartbeat(): int
    {
        dispatch(new HealthQueueJob($this->getCheckInstance()));

        return static::SUCCESS;
    }

    public function getCheckClass(): string
    {
        return QueueCheck::class;
    }
}
