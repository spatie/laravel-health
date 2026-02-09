<?php

declare(strict_types=1);

namespace Spatie\Health\Traits;

use Carbon\Carbon;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Str;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

/** @mixin Check */
trait HasFailedAfter
{
    protected int $failAfterMinutes = 0;

    protected ?string $cacheStoreName = null;

    protected string $cacheKeyPrefix = 'health:checks';

    public function failAfterMinutes(int $minutes): self
    {
        $this->failAfterMinutes = $minutes;

        return $this;
    }

    public function useCacheStore(string $cacheStoreName): self
    {
        $this->cacheStoreName = $cacheStoreName;

        return $this;
    }

    public function getCacheStoreName(): string
    {
        return $this->cacheStoreName ?? config('cache.default');
    }

    public function cacheKeyPrefix(string $cacheKeyPrefix): self
    {
        $this->cacheKeyPrefix = $cacheKeyPrefix;

        return $this;
    }

    protected function getCacheKeyPrefix(): string
    {
        return $this->cacheKeyPrefix;
    }

    public function getCacheKey(): string
    {
        return implode(':', [
            $this->getCacheKeyPrefix(),
            Str::slug($this->getName()),
            'firstFailureAt',
        ]);
    }

    protected function cacheStore(): Repository
    {
        return cache()->store($this->cacheStoreName);
    }

    protected function handleFailure(string $message = ''): Result
    {
        if ($this->failAfterMinutes <= 0) {
            return $this->failedResult($message);
        }

        $cache = $this->cacheStore();
        $cacheKey = $this->getCacheKey();
        $firstFailureAt = $cache->get($cacheKey);

        if ($firstFailureAt === null) {
            $cache->put($cacheKey, now());

            return $this->warningResult($message);
        }

        return Carbon::parse($firstFailureAt)->diffInMinutes() >= $this->failAfterMinutes
            ? $this->failedResult($message)
            : $this->warningResult($message);
    }

    protected function handleSuccess(string $message = ''): Result
    {
        $this->cacheStore()->forget($this->getCacheKey());

        return Result::make()->ok($message);
    }

    protected function warningResult(string $message = ''): Result
    {
        return Result::make()->warning($message);
    }

    protected function failedResult(string $message = ''): Result
    {
        return Result::make()->failed($message);
    }
}
