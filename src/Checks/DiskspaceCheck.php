<?php

namespace Spatie\Health\Checks;

use Spatie\Health\Checks\Check;
use Spatie\Health\Enums\Result;

class DiskspaceCheck extends Check
{
    public static function new(): static
    {
        $instance = new self();

        $instance->everyMinute();

        return $instance;
    }

    function name(): string
    {
        // TODO: Implement name() method.
    }

    function result(): Result
    {
        // TODO: Implement result() method.
    }

    function message(): ?string
    {
        // TODO: Implement message() method.
    }

    function meta(): array
    {
        // TODO: Implement meta() method.
    }

    public function warnWhenFreeSpaceIsBelowPercentage(int $int): self
    {
        return $this;
    }

    public function errorWhenFreeSpaceIsBelowPercentage(int $int): self
    {
        return $this;
    }
}
