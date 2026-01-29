<?php

namespace Spatie\Health\Checks\Checks;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;
use Spatie\Health\Exceptions\InvalidCheck;

class PingCheck extends Check
{
    protected ?string $url = null;

    protected ?string $failureMessage = null;

    protected int $timeout = 1;

    protected int $retryTimes = 1;

    protected string $method = 'GET';

    /** @var array<string, string> */
    protected array $headers = [];

    protected int $failAfterMinutes = 0;

    protected ?string $cacheStoreName = null;

    protected string $cacheKeyPrefix = 'health:checks:ping';

    public function url(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function timeout(int $seconds): self
    {
        $this->timeout = $seconds;

        return $this;
    }

    public function method(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function retryTimes(int $times): self
    {
        $this->retryTimes = $times;

        return $this;
    }

    /**
     * @param  array<string, string>  $headers
     * @return $this
     */
    public function headers(array $headers = []): self
    {
        $this->headers = $headers;

        return $this;
    }

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

    public function failureMessage(string $failureMessage): self
    {
        $this->failureMessage = $failureMessage;

        return $this;
    }

    public function run(): Result
    {
        if (is_null($this->url)) {
            return Result::make()
                ->failed()
                ->shortSummary(InvalidCheck::urlNotSet()->getMessage());
        }

        try {
            $request = Http::timeout($this->timeout)
                ->withHeaders($this->headers)
                ->retry($this->retryTimes)
                ->send($this->method, $this->url);

            if (! $request->successful()) {
                return $this->handleFailure();
            }
        } catch (Exception) {
            return $this->handleFailure();
        }

        cache()->store($this->cacheStoreName)->forget($this->getCacheKey());

        return Result::make()
            ->ok()
            ->shortSummary('Reachable');
    }

    protected function handleFailure(): Result
    {
        if ($this->failAfterMinutes <= 0) {
            return $this->failedResult();
        }

        $cacheKey = $this->getCacheKey();
        $firstFailureAt = cache()->store($this->cacheStoreName)->get($cacheKey);

        if ($firstFailureAt === null) {
            cache()->store($this->cacheStoreName)->put($cacheKey, now());

            return $this->warningResult();
        }

        $failingSince = Carbon::parse($firstFailureAt);
        if ($failingSince->diffInMinutes(now()) >= $this->failAfterMinutes) {
            return $this->failedResult();
        }

        return $this->warningResult();
    }

    protected function warningResult(): Result
    {
        return Result::make()
            ->warning()
            ->shortSummary('Unreachable')
            ->notificationMessage($this->failureMessage ?? "Pinging {$this->getName()} is failing.");
    }

    protected function failedResult(): Result
    {
        return Result::make()
            ->failed()
            ->shortSummary('Unreachable')
            ->notificationMessage($this->failureMessage ?? "Pinging {$this->getName()} failed.");
    }

    protected function getCacheKey(): string
    {
        return $this->cacheKeyPrefix . ':' . Str::slug($this->getName()) . ':firstFailureAt';
    }
}
