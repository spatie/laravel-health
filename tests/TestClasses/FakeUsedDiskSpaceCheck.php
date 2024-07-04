<?php

namespace Spatie\Health\Tests\TestClasses;

use Closure;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;

class FakeUsedDiskSpaceCheck extends UsedDiskSpaceCheck
{
    protected int $fakeDiskUsagePercentage = 0;

    public function fakeDiskUsagePercentage($fakePercentage): self
    {
        $this->fakeDiskUsagePercentage = $fakePercentage;

        return $this;
    }

    public function getDiskUsagePercentage(): int
    {
        return $this->fakeDiskUsagePercentage;
    }

    public function getFilesystemName(): ?string
    {
        return $this->filesystemName;
    }

    public function replyWith(Closure $closure): self
    {
        $this->closure = $closure;

        return $this;
    }
}
