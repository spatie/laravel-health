<?php

namespace Spatie\Health\Commands;

use Illuminate\Console\Command;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Checks\ScheduleCheck;
use Spatie\Health\Facades\Health;

class ScheduleCheckHeartbeatCommand extends Command
{
    protected $signature = 'health:schedule-check-heartbeat';

    public function handle(): int
    {
        /** @var ScheduleCheck|null $scheduleCheck */
        $scheduleCheck = Health::registeredChecks()->first(
            fn (Check $check) => $check instanceof ScheduleCheck
        );

        if (! $scheduleCheck) {
            $this->error("In order to use this command, you should register the `Spatie\Health\Checks\Checks\ScheduleCheck`");

            return static::FAILURE;
        }

        $cacheKey = $scheduleCheck->getCacheKey();

        if (! $cacheKey) {
            $this->error("You must set the `cacheKey` of `Spatie\Health\Checks\Checks\ScheduleCheck` to a non-empty value");

            return static::FAILURE;
        }

        cache()->store($scheduleCheck->getCacheStoreName())->set($cacheKey, now()->timestamp);

        return static::SUCCESS;
    }
}
