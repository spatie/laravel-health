<?php

namespace Spatie\Health\Checks;

use Carbon\Carbon;

trait HasHeartbeatCheck
{
    protected ?string $cacheKey = null;

    protected ?string $cacheStoreName = null;

    protected int $heartbeatMaxAgeInMinutes = 1;

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
        $result = Result::make();

        $lastHeartbeatTimestamp = cache()->store($this->cacheStoreName)->get($this->cacheKey);

        if (! $lastHeartbeatTimestamp) {
            return $result->failed("The {$this->getName()} check did not run yet.");
        }

        $latestHeartbeatAt = Carbon::createFromTimestamp($lastHeartbeatTimestamp);

        $minutesAgo = $latestHeartbeatAt->diffInMinutes() + 1;

        if ($minutesAgo > $this->heartbeatMaxAgeInMinutes) {
            return $result->failed("The last run of the {$this->getName()} check was more than {$minutesAgo} minutes ago.");
        }

        return $result->ok();
    }
}
