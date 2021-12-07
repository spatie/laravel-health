---
title: Registering the same check multiple times
weight: 5
---

You might want to register some of the more generic checks more than once. Image for instance that you want to use the [`PingCheck`](/docs/laravel-health/v1/available-checks/ping) to ping multiple sites.

```php
// ⛔️ this will not work

use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\PingCheck;

Health::checks([
    PingCheck::new()->url('https://your-site.com'),
    PingCheck::new()->url('https://another-site.com'),
]);
```

This will throw an exception as each check name should be unique. 

By default, the name of the check is its base class name. To override that default name, call `name` on the check.

```php
// 👍 this will work

use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\PingCheck;

Health::checks([
    PingCheck::new()->name('Your site ping check')->url('https://your-site.com'),
    PingCheck::new()->name('Another site ping check')->url('https://another-site.com'),
]);
```
