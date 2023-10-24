<?php

namespace Spatie\Health\Tests\TestClasses;

use Closure;
use Spatie\Health\Checks\Checks\CacheCheck;

class FakeCacheCheck extends CacheCheck
{
    protected Closure $closure;

    public function replyWith(Closure $closure): self
    {
        $this->closure = $closure;

        return $this;
    }

    public function canWriteValuesToCache(?string $driver): bool
    {
        return ($this->closure)();
    }
}
