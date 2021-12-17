<?php

namespace Spatie\Health\Checks\Checks;

use Exception;
use function app;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;
use Throwable;

class CacheCheck extends Check
{
    protected ?string $driver = null;

    public function driver(string $driver): self
    {
        $this->driver = $driver;

        return $this;
    }

    public function run(): Result
    {
        $driver = $this->driver ?? $this->defaultDriver();

        $result = Result::make()->meta([
            'driver' => $driver,
        ]);

        try {
            $response = $this->pingCache($driver);

            return $response
                ? $result->ok()
                : $result->failed("Could not set or retrieve an application cache value.");
        } catch (Exception $exception) {
            return $result->failed("An exception occurred with the application cache: `{$exception->getMessage()}`");
        }
    }

    protected function defaultDriver(): string
    {
        return config('cache.default', 'file');
    }

    protected function pingCache(string $driver): bool
    {
        $payload = Str::random(5);

        Cache::driver($driver)->put('laravel-health:check', $payload, 10);

        return (Cache::driver($driver)->get('laravel-health:check') === $payload);
    }
}
