<?php

namespace Spatie\Health\Checks\Checks;

use Exception;
use Illuminate\Support\Facades\Http;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;
use Spatie\Health\Exceptions\InvalidCheck;
use Spatie\Health\Traits\HasFailedAfter;

class PingCheck extends Check
{
    use HasFailedAfter;

    protected ?string $url = null;

    protected ?string $failureMessage = null;

    protected int $timeoutMs = 1000;

    protected int $retryTimes = 1;

    protected string $method = 'GET';

    /** @var array<string, string> */
    protected array $headers = [];

    public function url(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function timeout(int $seconds): self
    {
        $this->timeoutMs = $seconds * 1000;

        return $this;
    }

    public function timeoutMs(int $milliseconds): self
    {
        $this->timeoutMs = $milliseconds;

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

    public function failureMessage(string $failureMessage): self
    {
        $this->failureMessage = $failureMessage;

        return $this;
    }

    protected function getCacheKeyPrefix(): string
    {
        return 'health:checks:ping';
    }

    public function run(): Result
    {
        if (is_null($this->url)) {
            return Result::make()
                ->failed()
                ->shortSummary(InvalidCheck::urlNotSet()->getMessage());
        }

        $timeoutInSeconds = $this->timeoutMs / 1000;

        try {
            $request = Http::withOptions(['timeout' => $timeoutInSeconds, 'connect_timeout' => $timeoutInSeconds])
                ->withHeaders($this->headers)
                ->retry($this->retryTimes)
                ->send($this->method, $this->url);

            if (! $request->successful()) {
                return $this->handleFailure();
            }
        } catch (Exception) {
            return $this->handleFailure();
        }

        return $this->handleSuccess()
            ->shortSummary('Reachable');
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
}
