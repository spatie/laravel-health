<?php

namespace Spatie\Health\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

trait Pingable
{
    protected int $timeout = 3; // seconds

    protected int $retryTimes = 1;

    protected function pingUrl(?string $url = null): void
    {
        if (! $url || empty($url)) {
            return;
        }

        if (! $this->isValidUrl($url)) {
            Log::error("Invalid URL provided for health check ping: {$url}");

            return;
        }

        try {
            Http::timeout($this->timeout)
                ->retry($this->retryTimes)
                ->get($url);
        } catch (\Exception $e) {
            Log::error('Failed to ping health check URL: '.$e->getMessage());
        }
    }

    protected function isValidUrl(string $url): bool
    {
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        return true;
    }

    public function pingTimeout(int $seconds): self
    {
        if ($seconds <= 0) {
            throw new InvalidArgumentException('Timeout must be a positive integer.');
        }
        $this->timeout = $seconds;

        return $this;
    }

    public function pingRetryTimes(int $times): self
    {
        $this->retryTimes = $times;

        return $this;
    }
}
