<?php

namespace Spatie\Health\Exceptions;

use Exception;
use Spatie\Health\Checks\Check;

class CheckDidNotComplete extends Exception
{
    public static function make(Check $check, Exception $exception)
    {
        return new static(
            message: "The check named `{$check->name()}` did not complete. An exception was thrown with this message: `{$exception->getMessage()}`",
            previous: $exception,
        );
    }
}
