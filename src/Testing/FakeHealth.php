<?php

namespace Spatie\Health\Testing;

use Closure;
use Illuminate\Support\Collection;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;
use Spatie\Health\Health;

class FakeHealth extends Health
{
    /**
     * @param  array<class-string<Check>, Result|FakeValues|(Closure(Check): Result|FakeValues)>  $fakeChecks
     */
    public function __construct(
        private Health $decoratedHealth,
        private array $fakeChecks
    ) {}

    public function registeredChecks(): Collection
    {
        return $this->decoratedHealth->registeredChecks()->map(
            fn (Check $check) => array_key_exists($check::class, $this->fakeChecks)
                ? $this->buildFakeCheck($check, $this->fakeChecks[$check::class])
                : $check
        );
    }

    /**
     * @param  Result|FakeValues|(Closure(Check): Result|FakeValues)  $result
     */
    protected function buildFakeCheck(Check $decoratedCheck, Result|FakeValues|Closure $result): FakeCheck
    {
        // @phpstan-ignore-next-line
        $result = FakeValues::from(value($result, $decoratedCheck));

        return FakeCheck::new()->fake($decoratedCheck, $result);
    }
}
