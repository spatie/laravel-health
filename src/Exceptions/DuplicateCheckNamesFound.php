<?php

namespace Spatie\Health\Exceptions;

use Exception;
use Illuminate\Support\Collection;

class DuplicateCheckNamesFound extends Exception
{
    public static function make(Collection $duplicateCheckNames): self
    {
        $duplicateCheckNamesString = $duplicateCheckNames
            ->map(fn (string $name) => "`{$name}`")
            ->join(', ', ' and ');

        return new self("You registered checks with a non-unique name: {$duplicateCheckNamesString}. Each check should be unique. If you really want to use the same check class twice, make sure to call `name()` on them to ensure that they all have unique names.");
    }
}
