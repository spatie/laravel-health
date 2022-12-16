<?php

namespace Spatie\Health\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Health\Checks\Checks\HeartbeatCheck;

class HealthQueueJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected HeartbeatCheck $queueCheck;

    public function __construct(HeartbeatCheck $queueCheck)
    {
        $this->queueCheck = $queueCheck;
    }

    public function handle()
    {
        $cacheStore = $this->queueCheck->getCacheStoreName();
        $cacheKey = $this->queueCheck->getCacheKey();

        cache()->store($cacheStore)->set($cacheKey, now()->timestamp);
    }
}
