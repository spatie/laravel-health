---
title: Registering your first check
weight: 1
---

This package offers [various checks](https://spatie.be/docs/laravel-health/v1/available-checks/overview) to monitor different aspects of your application.

You can register the checks you want to run, by passing an array with checks to `Spatie\Health\Facades\Health::check()`.

Here's an example where we're going to register the `UsedDiskSpace` and `DatabaseCheck`. Typically, you would put this in a service provider.

```php
// typically, in a service provider

use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;
use Spatie\Health\Checks\Checks\DatabaseCheck;

Health::checks([
    new UsedDiskSpaceCheck(),
    new DatabaseCheck(),
]);
```

All registered checks will run when the `RunChecksCommand` executes. If you followed [the installation instructions](https://spatie.be/docs/laravel-health/v1/installation-setup), you have already scheduled that command to execute every minute.

When a check results in a warning or a failure, a notification will be sent. You can learn more about notifications in [the section on configuring notifications](https://spatie.be/docs/laravel-health/v1/configuring-notifications/general).

Though it's not required, you can store the results of the checks. This way external services like [Oh Dear](https://ohdear.app) can monitor the health check results, or you can keep a history of [the results in your own database]().
