<?php

namespace Spatie\Health\Checks\Checks;

use Carbon\Carbon;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

class ScheduleCheck extends Check
{
    protected string $cacheKey = 'health.checks.schedule.latestHeartbeatAt';

    protected int $heartbeatMaxAgeInMinutes = 1;

    public function cacheKey(string $cacheKey): self
    {
        $this->cacheKey = $cacheKey;

        return $this;
    }

    public function heartbeatMaxAgeInMinutes(int $heartbeatMaxAgeInMinutes): self
    {
        $this->heartbeatMaxAgeInMinutes = $heartbeatMaxAgeInMinutes;

        return $this;
    }

    public function getCacheKey(): string
    {
        return $this->cacheKey;
    }

    public function run(): Result
    {
        $result = Result::make()
            ->ok();

        $lastHeartbeatTimestamp = cache()->get($this->cacheKey);

        if (! $lastHeartbeatTimestamp) {
            return $result->failed('The schedule did not run yet.');
        }

        $latestHeartbeatAt = Carbon::createFromTimestamp($lastHeartbeatTimestamp);

        $minutesAgo = $latestHeartbeatAt->diffInMinutes() + 1;

        if ($minutesAgo > $this->heartbeatMaxAgeInMinutes) {
            return $result->failed("The last run of the schedule was more than {$minutesAgo} minutes ago.");
        }

        return $result;
    }
}
