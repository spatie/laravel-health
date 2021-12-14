<?php

namespace Spatie\Health\Exceptions;

use Exception;

class InvalidTheme extends Exception
{
    public static function themeIsInvalid(mixed $invalidValue): self
    {
        return new self("You tried to use an invalid theme, `{$invalidValue}`. Valid themes are 'light' or 'dark'.");
    }
}
