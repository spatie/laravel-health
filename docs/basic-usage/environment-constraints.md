---
title: Environment Constraints
weight: 6
---

The `environments` method may be used to execute checks only on the given environments (as defined by the APP_ENV environment variable). You can specify a single environment string or an array of envrionments. Here's an example of checks with environment constraints:

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\OptimizedAppCheck;
use Spatie\Health\Checks\Checks\ScheduleCheck;

Health::checks([
    OptimizedAppCheck::new()->environments('production'),
    ScheduleCheck::new()->environments(['staging','production']),
]);
```
