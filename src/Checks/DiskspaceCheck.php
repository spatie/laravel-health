<?php

namespace Spatie\Health\Checks;

use Spatie\Health\Enums\Result;

class DiskspaceCheck extends Check
{
    public static function new(): static
    {
        $instance = new self();

        $instance->everyMinute();

        return $instance;
    }

    public function name(): string
    {
        // TODO: Implement name() method.
    }

    public function result(): Result
    {
        // TODO: Implement result() method.
    }

    public function message(): ?string
    {
        // TODO: Implement message() method.
    }

    public function meta(): array
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
