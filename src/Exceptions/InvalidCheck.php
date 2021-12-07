<?php

namespace Spatie\Health\Exceptions;

use Exception;
use Spatie\Health\Checks\Check;

class InvalidCheck extends Exception
{
    public static function doesNotExtendCheck(mixed $invalidValue): self
    {
        $checkClass = Check::class;

        $extraMessage = '';

        if (is_string($invalidValue)) {
            $extraMessage = " You tried to register a string `{$invalidValue}`";
        }

        if (is_object($invalidValue)) {
            $invalidClass = $invalidValue::class;

            $extraMessage = " You tried to register class `{$invalidClass}`";
        }

        return new self("You tried to register an invalid check. A valid check should extend `$checkClass`.{$extraMessage}");
    }

    public static function urlNotSet(): self
    {
        return new self('When using the `PingCheck` you must call `url` to pass the URL you want to ping.');
    }
}
