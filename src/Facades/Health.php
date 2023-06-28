<?php

namespace Spatie\Health\Facades;

use Closure;
use Illuminate\Support\Facades\Facade;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;
use Spatie\Health\Testing\FakeHealth;
use Spatie\Health\Testing\FakeValues;

/**
 * @mixin \Spatie\Health\Health
 */
class Health extends Facade
{
    /**
     * @param  array<class-string<Check>, Result|FakeValues|(Closure(Check): Result|FakeValues)>  $checks
     */
    public static function fake(array $checks = []): FakeHealth
    {
        $fake = (new FakeHealth(static::getFacadeRoot(), $checks));

        static::swap($fake);
        static::swapAlias($fake);

        return $fake;
    }

    protected static function swapAlias(FakeHealth $fakeHealth): void
    {
        static::$resolvedInstance[\Spatie\Health\Health::class] = $fakeHealth;
        static::$app->instance(\Spatie\Health\Health::class, $fakeHealth);
    }

    protected static function getFacadeAccessor()
    {
        return 'health';
    }
}
