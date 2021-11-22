<?php

namespace Spatie\Health\Tests\TestClasses;

use Closure;
use Spatie\Health\Checks\Checks\RedisCheck;

class FakeRedisCheck extends RedisCheck
{
    protected Closure $closure;

    public function replyWith(Closure $closure): self
    {
        $this->closure = $closure;

        return $this;
    }

    public function pingRedis(): bool|string
    {
        return ($this->closure)();
    }
}
