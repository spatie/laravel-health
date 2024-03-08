<?php

namespace Spatie\Health\Checks\Checks;

use Carbon\Carbon;
use Composer\InstalledVersions;
use Illuminate\Support\Arr;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

class QueueCheck extends Check
{
    protected ?string $cacheKey = 'health:checks:queue:latestHeartbeatAt';

    protected ?string $cacheStoreName = null;

    protected int $failWhenTestJobTakesLongerThanMinutes = 5;

    protected ?array $onQueues;

    public function useCacheStore(string $cacheStoreName): self
    {
        $this->cacheStoreName = $cacheStoreName;

        return $this;
    }

    public function getCacheStoreName(): string
    {
        return $this->cacheStoreName ?? config('cache.default');
    }

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

    public function getCacheKey(string $queue): string
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
            $lastHeartbeatTimestamp = cache()->store($this->cacheStoreName)->get($this->getCacheKey($queue));

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

        $result = Result::make();

        if (! empty($fails)) {
            $result->meta($fails);

            return $result->failed('Queue jobs running failed. Check meta for more information.');
        }

        return $result->ok();
    }
}
