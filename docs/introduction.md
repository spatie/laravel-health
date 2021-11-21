---
title: Introduction
weight: 1
---

**THIS PACKAGE IS IN DEVELOPMENT. DO NOT USE IN PRODUCTION (YET)**

Using this package you can monitor the health of your application by registering checks.

Here's an example where we'll monitor available disk space.

```php
// typically, in a service provider

use Spatie\Health\Facades\Health;
use \Spatie\Health\Checks\DiskSpaceCheck;

Health::checks([
    DiskSpaceCheck::new()
        ->warnWhenUsedSpaceIsAbovePercentage(70)
        ->errorWhenUsedSpaceIsAbovePercentage(90);
]);
```

When the used disk space is over 70%, then a notification with a warning will be sent. If it's above 90%, you'll get an error notification. Out of the box, the package can notify you via mail and Slack.
