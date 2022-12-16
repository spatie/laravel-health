<?php

namespace Spatie\Health\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\Health\Checks\Checks\HeartbeatCheck;

class HealthQueueJob implements ShouldQueue
{
    protected HeartbeatCheck $queueCheck;

    public function __construct(HeartbeatCheck $queueCheck)
    {
        $this->queueCheck = $queueCheck;
    }

    public function handle(): void
    {
        $cacheStore = $this->queueCheck->getCacheStoreName();
        $cacheKey = $this->queueCheck->getCacheKey();

        cache()->store($cacheStore)->set($cacheKey, now()->timestamp);
    }
}
