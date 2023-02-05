<?php

namespace Spatie\Health\Checks\Checks;

use Illuminate\Redis\Connections\PhpRedisConnection;
use Illuminate\Redis\Connections\PredisConnection;
use Illuminate\Support\Facades\Redis;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

class RedisMemoryUsageCheck extends Check
{
    protected string $connectionName = 'default';

    protected float $failWhenAboveMb = 500;

    public function connectionName(string $connectionName): self
    {
        $this->connectionName = $connectionName;

        return $this;
    }

    public function failWhenAboveMb(float $errorThresholdMb): self
    {
        $this->failWhenAboveMb = $errorThresholdMb;

        return $this;
    }

    public function run(): Result
    {
        $result = Result::make()->meta([
            'connection_name' => $this->connectionName,
        ]);

        $memoryUsage = $this->getMemoryUsageInMb();

        if ($memoryUsage >= $this->failWhenAboveMb) {
            return $result->failed("Redis memory usage is {$memoryUsage} MB, which is above the threshold of {$this->failWhenAboveMb} MB.");
        }

        return $result->ok();
    }

    protected function getMemoryUsageInMb(): float
    {
        $redis = Redis::connection($this->connectionName);

        $memoryUsage = (int) match (get_class($redis)) {
            PhpRedisConnection::class => $redis->info()['used_memory'],
            PredisConnection::class => $redis->info()['Memory']['used_memory'],
            default => null,
        };

        return round($memoryUsage / 1024 / 1024, 2);
    }
}
