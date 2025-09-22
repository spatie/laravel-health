<?php

namespace Spatie\Health\Checks\Checks;

use Exception;
use Illuminate\Support\Facades\Redis;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

use function __;

class RedisCheck extends Check
{
    protected string $connectionName = 'default';

    public function __construct()
    {
        parent::__construct();
        $this->label(__('health::checks.titles.redis'));
    }

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
        } catch (Exception $exception) {
            return $result->failed(__('health::checks.redis.connection_exception', [
                'message' => $exception->getMessage(),
            ]));
        }

        if ($response === false) {
            return $result->failed(__('health::checks.redis.falsy_response'));
        }

        return $result->ok();
    }

    protected function pingRedis(): bool|string
    {
        return Redis::connection($this->connectionName)->ping();
    }
}
