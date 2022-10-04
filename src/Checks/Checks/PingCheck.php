<?php

namespace Spatie\Health\Checks\Checks;

use Exception;
use Illuminate\Support\Facades\Http;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;
use Spatie\Health\Exceptions\InvalidCheck;

class PingCheck extends Check
{
    public ?string $url = null;

    public ?string $failureMessage = null;

    public int $timeout = 1;
    
    public int $retry = 1;

    public string $method = 'GET';

    /** @var array<string, string> */
    public array $headers = [];

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
        $this->retry = $times;

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

    public function run(): Result
    {
        if (is_null($this->url)) {
            throw InvalidCheck::urlNotSet();
        }

        try {
            $request = Http::timeout($this->timeout)
                ->withHeaders($this->headers)
                ->retry($this->retry)
                ->send($this->method, $this->url);

            if (! $request->successful()) {
                return $this->failedResult();
            }
        } catch (Exception) {
            return $this->failedResult();
        }

        return Result::make()
            ->ok()
            ->shortSummary('Reachable');
    }

    protected function failedResult(): Result
    {
        return Result::make()
            ->failed()
            ->shortSummary('Unreachable')
            ->notificationMessage($this->failureMessage ?? "Pinging {$this->getName()} failed.");
    }
}
