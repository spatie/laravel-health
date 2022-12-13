<?php

namespace Spatie\Health\Exceptions;

use Exception;
use Illuminate\Database\ConnectionInterface;

class DatabaseNotSupported extends Exception
{
    public static function make(ConnectionInterface $connection): self
    {
        return new self("The database driver `{$connection->getDriverName()}` is not supported by this package.");
    }
}
