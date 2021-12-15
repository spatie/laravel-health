---
title: Introduction
weight: 1
---

Using this package you can monitor the health of your application by registering one of [the available checks](/docs/laravel-health/v1/available-checks/overview).

Here's an example where we'll monitor used disk space.

```php
// typically, in a service provider

use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;

Health::checks([
    UsedDiskSpaceCheck::new()
        ->warnWhenUsedSpaceIsAbovePercentage(70)
        ->failWhenUsedSpaceIsAbovePercentage(90),
]);
```

When the used disk space is over 70%, then a notification with a warning will be sent. If it's above 90%, you'll get an error notification. Out of the box, the package can notify you via mail and Slack.

You'll find a [list of available checks](/docs/laravel-health/v1/available-checks/overview) here. Need a custom check? No problem, you [can create your own check](/docs/laravel-health/v1/basic-usage/creating-custom-checks) in no time.

The package can also display [a beautiful overview](/docs/laravel-health/v1/viewing-results/on-a-webpage) of all health check results.

![image](/docs/laravel-health/v1/images/list-web-dark.png)
