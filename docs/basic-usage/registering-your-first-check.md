---
title: Registering your first check
weight: 1
---

This package offers [various checks](https://spatie.be/docs/laravel-health/v1/available-checks/overview) to monitor different aspects of your application.

You can register the checks you want to run, by passing an array with checks to `Spatie\Health\Facades\Health::check()`.

Here's an example where we're going to register the `UsedDiskSpace` and `DatabaseCheck`. Typically, you would put this in [a service provider of your own](https://laravel.com/docs/12.x/providers#writing-service-providers).

```php
// typically, in a service provider

use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;
use Spatie\Health\Checks\Checks\DatabaseCheck;

Health::checks([
    UsedDiskSpaceCheck::new(),
    DatabaseCheck::new(),
]);
```

All registered checks will run when the `RunHealthChecksCommand` executes. If you followed [the installation instructions](https://spatie.be/docs/laravel-health/v1/installation-setup), you have already scheduled that command to execute every minute.

If you haven't scheduled that command, you could run the checks and view the results [via HTTP](https://spatie.be/docs/laravel-health/v1/viewing-results/on-a-webpage) or [JSON](https://spatie.be/docs/laravel-health/v1/viewing-results/as-json).

When a check results in a warning or a failure, a notification will be sent. You can learn more about notifications in [the section on configuring notifications](https://spatie.be/docs/laravel-health/v1/configuring-notifications/general).

Though it's not required, you can store the results of the checks. 
This way, you can keep a history of [the results in your own database](https://spatie.be/docs/laravel-health/v1/storing-results/in-the-database).

## Frequency
You can configure how often the checks should run. By default, the checks will run every minute. You can change this by calling a method that defines how often it should run, or passing a cron expression. Please refer to [the Laravel documentation](https://laravel.com/docs/12.x/scheduling#schedule-frequency-options) to see which options are available. 

```php
Health::checks([
    UsedDiskSpaceCheck::new()->daily(),
    DatabaseCheck::new(),
]);
```

Additionally, you might also choose the timezone in which the checks should run. By default, the timezone is set to `UTC`. You can change this by calling the `timezone` method.

```php
Health::checks([
    UsedDiskSpaceCheck::new()->dailyAt('02:00')->timezone('America/Los_Angeles'),
    DatabaseCheck::new()->dailyAt('02:00'),
]);
```

The `UsedDiskSpaceCheck` check will run every day at 2AM Los Angeles time, whereas `DatabaseCheck` will run every day at 2AM UTC.
