---
title: Conditionally running or modifying checks
weight: 5
---

This package provides methods to run certain checks only when specified conditions are met.

If you would like to conditionally run a check, you can use the `if` and `unless` methods.
For more control, you can also use callables. They are evaluated every time a health check is run.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\DebugModeCheck;
use Spatie\Health\Checks\Checks\RedisCheck;

Health::checks([
    DebugModeCheck::new()->unless(app()->environment('local')),
    RedisCheck::new()->if(fn () => app(SomeHeavyService::class)->shouldCheckHealth()),
]);
```

## Custom condition methods

You may find yourself repeating conditions for multiple checks. To avoid that, 
you can register a Laravel macro on a check with a custom condition method.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Checks\DebugModeCheck;
use Spatie\Health\Checks\Checks\RedisCheck;

Check::macro('ifEnvironment', fn (string|array $envs) => $this->if(fn () => app()->environment($envs)));

Health::checks([
    DebugModeCheck::new()->ifEnvironment('production')
]);
```

## Chaining conditions

Sometimes you need more than one condition on a check, so you may chain two or more of them
simply by calling `if` or `unless` multiple times. They are evaluated in the order that 
you define them.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\DebugModeCheck;
use Spatie\Health\Checks\Checks\RedisCheck;

Health::checks([
    DebugModeCheck::new()
        ->unless(app()->environment('local'))
        ->if(fn () => app(SomeHeavyService::class)->shouldCheckHealth()),
]);
```

## Modifying checks on a condition

You may want to slightly change check's configuration under a specific condition. You can do
so using `when` and `doUnless` methods. In this example, a smaller memory limit is enforced 
on a local environment.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\RedisMemoryUsageCheck;

Health::checks([
    RedisMemoryUsageCheck::new()
        ->failWhenAboveMb(1000)
        ->when(
            app()->environment('local'), 
            fn (RedisMemoryUsageCheck $check) => $check->failWhenAboveMb(200)
        ),
]);
```
