<?php

namespace Spatie\Health\Tests\TestClasses;

use Spatie\Health\Checks\Checks\DatabaseSizeCheck;

class FakeDatabaseSizeCheck extends DatabaseSizeCheck
{
    protected float $fakeDatabaseSizeInGb = 0;

    public function fakeDatabaseSizeInGb(float $fakeDatabaseSizeInGb): self
    {
        $this->fakeDatabaseSizeInGb = $fakeDatabaseSizeInGb;

        return $this;
    }

    public function getDatabaseSizeInGb(): float
    {
        return $this->fakeDatabaseSizeInGb;
    }
}
