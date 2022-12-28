<?php

namespace Spatie\Health\Commands;

use Illuminate\Console\Command;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Checks\QueueCheck;
use Spatie\Health\Facades\Health;
use Spatie\Health\Jobs\HealthQueueJob;

class DispatchQueueCheckJobsCommand extends Command
{
    protected $signature = 'health:queue-check-heartbeat';

    public function handle(): int
    {
        /** @var QueueCheck|null $queueCheck */
        $queueCheck = Health::registeredChecks()->first(
            fn (Check $check) => $check instanceof QueueCheck
        );

        foreach ($queueCheck->getQueues() as $queue) {
            HealthQueueJob::dispatch($queueCheck)->onQueue($queue);
        }

        return static::SUCCESS;
    }
}
