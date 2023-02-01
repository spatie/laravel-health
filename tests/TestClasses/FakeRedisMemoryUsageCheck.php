<?php

namespace Spatie\Health\Tests\TestClasses;

use Spatie\Health\Checks\Checks\RedisMemoryUsageCheck;

class FakeRedisMemoryUsageCheck extends RedisMemoryUsageCheck
{
    protected float $fakeMemoryUsageInMb;

    public function fakeMemoryUsageInMb(float $fakeMemoryUsageInMb): self
    {
        $this->fakeMemoryUsageInMb = $fakeMemoryUsageInMb;

        return $this;
    }

    public function getMemoryUsageInMb(): float
    {
        return $this->fakeMemoryUsageInMb;
    }
}
