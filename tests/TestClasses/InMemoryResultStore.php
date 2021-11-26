<?php

namespace Spatie\Health\Tests\TestClasses;

use Illuminate\Support\Collection;
use Pest\Expectation;
use Pest\Support\Extendable;
use Spatie\Health\ResultStores\Report\Report;
use Spatie\Health\ResultStores\ResultStore;
use Spatie\Health\Checks\Result;

class InMemoryResultStore implements ResultStore
{
    /** @var \Illuminate\Support\Collection<int, Result>  */
    public static Collection $checkResults;

    public function save(Collection $checkResults): void
    {
        self::$checkResults = $checkResults;
    }

    public function latestReport(): ?Report
    {
        // TODO: Implement latestReport() method.
    }

    public static function expectCheckResults(): Expectation|Extendable
    {
        return expect(self::$checkResults);
    }
}
