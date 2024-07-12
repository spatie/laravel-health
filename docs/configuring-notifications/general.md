---
title: General
weight: 1
---

Whenever one of the checks fails, the package can send you a notification. A notification will be sent for all checks that send a message in [their result](/docs/laravel-health/v1/basic-usage/creating-custom-checks#creating-results).

Out of the box, the notification can be sent:

- via [mail](/docs/laravel-health/v1/configuring-notifications/via-mail), 
- via [Slack](/docs/laravel-health/v1/configuring-notifications/via-slack)
- via [Oh Dear](/docs/laravel-health/v1/configuring-notifications/via-oh-dear) (enables snoozing notifications, and delivery via Telegram, Discord, MS Teams, webhooks, ...)

## Disabling notifications

You may want to disable notifications for certain types of checks, but still have them show up on the page or cli.
To do this you can use the `disableNotifications` method. If you would like to disable notifications for certain check
statuses, the `disableNotificationsOnWarning` and `disableNotificationsOnFailure` methods are available.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\CpuLoadCheck;

Health::checks([
    CpuLoadCheck::new()
        ->disableNotifcations()
]);
```

## Throttling notifications

These notifications are throttled, so you don't get overwhelmed with notifications when something goes wrong. By default, you'll only get one notification per hour.

In the `throttle_notifications_for_minutes` key of the `health` config file, you can customize the length of the throttling period.

You can also configure the throttle time for notifications on an individual basis as well. If you want to customize the throttle
time based on the result of the check, the `throttleWarningNotificationsFor` and `throttleFailureNotificationsFor` methods are also available.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\CpuLoadCheck;

Health::checks([
    CpuLoadCheck::new()
        ->throttleNotificationsFor(24 * 60) // 1 day
]);
```

Note that when a notification for check with a custom throttle time is sent, the notification will include *all* the checks that failed,
not just the one with the custom throttle time. This avoids the false-positive feeling that an incomplete notification would have.
