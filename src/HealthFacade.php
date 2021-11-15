<?php

namespace Spatie\Health;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Spatie\Health\Health
 */
class HealthFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-health';
    }
}
