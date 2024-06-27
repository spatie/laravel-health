<?php

namespace Spatie\Health\Testing;

use Spatie\Health\Checks\Result;

class FakeValues
{
    public function __construct(
        private Result $result,
        private ?bool $shouldRun = null,
    ) {}

    public static function from(Result|self $values): self
    {
        if ($values instanceof Result) {
            return new self($values);
        }

        return $values;
    }

    public function getResult(): Result
    {
        return $this->result;
    }

    public function shouldRun(): ?bool
    {
        return $this->shouldRun;
    }
}
