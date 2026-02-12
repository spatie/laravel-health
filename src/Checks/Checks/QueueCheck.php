<?php

namespace Spatie\Health\Checks\Checks;

use Carbon\Carbon;
use Composer\InstalledVersions;
use Illuminate\Support\Arr;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;
use Spatie\Health\Traits\HasFailedAfter;

class QueueCheck extends Check
{
    use HasFailedAfter;

    protected ?string $cacheKey = 'health:checks:queue:latestHeartbeatAt';

    protected int $failWhenTestJobTakesLongerThanMinutes = 5;

    protected ?array $onQueues;

    public function cacheKey(string $cacheKey): self
    {
        $this->cacheKey = $cacheKey;

        return $this;
    }

    public function failWhenHealthJobTakesLongerThanMinutes(int $minutes): self
    {
        $this->failWhenTestJobTakesLongerThanMinutes = $minutes;

        return $this;
    }

    public function getHeartbeatCacheKey(string $queue): string
    {
        return "{$this->cacheKey}.{$queue}";
    }

    public function onQueue(array|string $queue): self
    {
        $this->onQueues = array_unique(Arr::wrap($queue));

        return $this;
    }

    public function getQueues(): array
    {
        return $this->onQueues ?? [$this->getDefaultQueue(config('queue.default'))];
    }

    protected function getDefaultQueue($connection)
    {
        return config("queue.connections.{$connection}.queue", 'default');
    }

    public function run(): Result
    {
        $fails = [];

        foreach ($this->getQueues() as $queue) {
            $lastHeartbeatTimestamp = $this->cacheStore()->get($this->getHeartbeatCacheKey($queue));

            if (! $lastHeartbeatTimestamp) {
                $fails[] = "The `{$queue}` queue did not run yet.";

                continue;
            }

            $latestHeartbeatAt = Carbon::createFromTimestamp($lastHeartbeatTimestamp);

            $carbonVersion = InstalledVersions::getVersion('nesbot/carbon');

            $minutesAgo = $latestHeartbeatAt->diffInMinutes();

            if (version_compare($carbonVersion,
                '3.0.0', '<')) {
                $minutesAgo += 1;
            }

            if ($minutesAgo > $this->failWhenTestJobTakesLongerThanMinutes) {
                $fails[] = "The last run of the `{$queue}` queue was more than {$minutesAgo} minutes ago.";
            }
        }

        if (! empty($fails)) {
            return $this->handleFailure('Queue jobs running failed. Check meta for more information.')
                ->meta($fails);
        }

        return $this->handleSuccess();
    }
}
