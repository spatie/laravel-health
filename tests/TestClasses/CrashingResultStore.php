<?php

namespace Spatie\Health\Tests\TestClasses;

use Exception;
use Illuminate\Support\Collection;
use Spatie\Health\ResultStores\ResultStore;

class CrashingResultStore implements ResultStore
{
    public function save(Collection $checkResults): void
    {
        throw new Exception('Store is crashing');
    }
}
