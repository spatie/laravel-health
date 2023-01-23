---
title: Horizon
weight: 12
---

This check will make sure Horizon is running.  It will report a warning when Horizon is paused, and a failure when Horizon is not running.

## Usage

Here's how you can register the check.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\HorizonCheck;

Health::checks([
    HorizonCheck::new(),
]);
```
