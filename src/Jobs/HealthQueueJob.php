<?php

namespace Spatie\Health\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\Health\Checks\Checks\QueueCheck;

class HealthQueueJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    protected QueueCheck $queueCheck;

    public function __construct(QueueCheck $queueCheck)
    {
        $this->queueCheck = $queueCheck;
    }

    public function handle(): void
    {
        $cacheStore = $this->queueCheck->getCacheStoreName();

        cache()
            ->store($cacheStore)
            ->set(
                $this->queueCheck->getCacheKey($this->queue),
                now()->timestamp,
            );
    }
}
