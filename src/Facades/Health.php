<?php

namespace Spatie\Health\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Spatie\Health\Health
 */
class Health extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'health';
    }
}
