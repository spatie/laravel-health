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

    protected ?float $warnWhenAboveMb = null;

    protected float $failWhenAboveMb = 500;

    public function connectionName(string $connectionName): self
    {
        $this->connectionName = $connectionName;

        return $this;
    }

    public function warnWhenAboveMb(float $errorThresholdMb): self
    {
        $this->warnWhenAboveMb = $errorThresholdMb;

        return $this;
    }

    public function failWhenAboveMb(float $errorThresholdMb): self
    {
        $this->failWhenAboveMb = $errorThresholdMb;

        return $this;
    }

    public function run(): Result
    {
        $memoryUsage = $this->getMemoryUsageInMb();

        $result = Result::make()->shortSummary("{$memoryUsage} MB used");

        $result->meta([
            'connection_name' => $this->connectionName,
            'memory_usage' => $memoryUsage,
        ]);

        $message = "Redis memory usage is {$memoryUsage} MB. The {threshold_type} threshold is {memory_threshold} MB.";

        if ($memoryUsage >= $this->failWhenAboveMb) {
            return $result->failed(strtr($message, [
                '{threshold_type}' => 'fail',
                '{memory_threshold}' => $this->failWhenAboveMb,
            ]));
        }
        if ($this->warnWhenAboveMb && $memoryUsage >= $this->warnWhenAboveMb) {
            return $result->warning(strtr($message, [
                '{threshold_type}' => 'warning',
                '{memory_threshold}' => $this->warnWhenAboveMb,
            ]));
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
