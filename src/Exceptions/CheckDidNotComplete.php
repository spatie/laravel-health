<?php

namespace Spatie\Health\Exceptions;

use Exception;
use Spatie\Health\Checks\Check;

class CheckDidNotComplete extends Exception
{
    public static function make(Check $check, Exception $exception): self
    {
        return new self(
            message: "The check named `{$check->getName()}` did not complete. An exception was thrown with this message: `".get_class($exception).": {$exception->getMessage()}`",
            previous: $exception,
        );
    }
}
