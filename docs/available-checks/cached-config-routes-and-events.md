---
title: Cached config, routes, and events
weight: 4
---

To improve performance, Laravel can cache configuration files, routes and events. Using the `OptimizedAppCheck` you can make sure these things are actually cached.

## Usage

Here's how you can register the check.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\OptimizedAppCheck;

Health::checks([
    OptimizedAppCheck::new(),
]);
```

This check will pass if the config, routes and events are cached.

Should this check fail for your app, then you should consider adding this command to your deployment procedure

```bash
php artisan optimize # will cache config, events, routes and views
```

If you prefer to cache things individually, you can use these commands instead:

```bash
php artisan config:cache
php artisan route:cache
php artisan event:cache
```

### Only check certain caches

By default, the check will make sure that config, routes and events are cached. If you only want to check certain caches, you can call the `checkConfig`, `checkRoutes` and `checkEvents` methods. In this example, we'll only check for cached config and routes.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\OptimizedAppCheck;

Health::checks([
    OptimizedAppCheck::new()
       ->checkConfig()
       ->checkRoutes(),
]);
```
