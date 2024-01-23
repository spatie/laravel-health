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

Should this check fail for your app, then you should consider adding these commands to your deployment procedure

```bash
php artisan optimize # will cache config and routes
php artisan event:cache # will cache events
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
