<?php

namespace Spatie\Health\Checks\Checks;

use Illuminate\Redis\Connections\PhpRedisClusterConnection;
use Illuminate\Support\Facades\Redis;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;
use Throwable;

class RedisCheck extends Check
{
    protected string $connectionName = 'default';

    public function connectionName(string $connectionName): self
    {
        $this->connectionName = $connectionName;

        return $this;
    }

    public function run(): Result
    {
        $result = Result::make()->meta([
            'connection_name' => $this->connectionName,
        ]);

        try {
            $response = $this->pingRedis();
        } catch (Throwable $exception) {
            return $result->failed("An exception occurred when connecting to Redis: `{$exception->getMessage()}`");
        }

        if ($response === false) {
            return $result->failed('Redis returned a falsy response when try to connection to it.');
        }

        return $result->ok();
    }

    protected function pingRedis(): bool|string
    {
        $connection = Redis::connection($this->connectionName);

        $response = $connection instanceof PhpRedisClusterConnection
            ? $connection->command('ping', ['laravel-health:redis:ping'])
            : $connection->ping();

        return is_string($response) ? $response : (bool) $response;
    }
}
