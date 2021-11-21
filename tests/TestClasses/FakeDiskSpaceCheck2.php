<?php

namespace Spatie\Health\Tests\TestClasses;

use Spatie\Health\Checks\DiskSpaceCheck2;

class FakeDiskSpaceCheck2 extends DiskSpaceCheck2
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
