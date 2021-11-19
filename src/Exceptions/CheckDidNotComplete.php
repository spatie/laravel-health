<?php

namespace Spatie\Health\Exceptions;

use Exception;
use Spatie\Health\Checks\Check;

final class CheckDidNotComplete extends Exception
{
    public static function make(Check $check, Exception $exception): self
    {
        return new self(
            message: "The check named `{$check->name()}` did not complete. An exception was thrown with this message: `{$exception->getMessage()}`",
            previous: $exception,
        );
    }
}
