---
title: General
weight: 1
---

This package provides several ways of listing the latest check results.

- [on a webpage](/docs/laravel-health/v1/viewing-results/on-a-webpage)
- [on the cli](/docs/laravel-health/v1/viewing-results/on-the-cli)
- [as JSON](/docs/laravel-health/v1/viewing-results/as-json)


## Customizing the titles of the checks

You can use the `label` function to customize the title that is shown on the health dashboard, and on the Oh Dear application health screen.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;

Health::checks([
    UsedDiskSpaceCheck::new()->label('Disk space on main disk'),
]);
```

## Running checks conditionally

If you would like to conditionally run a checks, you can use the if and unless methods.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\DebugModeCheck;
use Spatie\Health\Checks\Checks\RedisCheck;

Health::checks([
    DebugModeCheck::new()->if(app()->isProduction()),
    RedisCheck::new()->unless(app()->environment('development')),
]);
```
