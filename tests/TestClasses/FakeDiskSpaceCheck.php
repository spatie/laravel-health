<?php

namespace Spatie\Health\Tests\TestClasses;

use Spatie\Health\Checks\Checks\DiskSpaceCheck;

class FakeDiskSpaceCheck extends DiskSpaceCheck
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
}
