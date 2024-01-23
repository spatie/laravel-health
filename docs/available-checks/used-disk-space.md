---
title: Used disk space
weight: 21
---

This check will monitor the percentage of available disk space.

By default, this check will send:
- a warning  when the used disk space is above 70%
- a failure when the used disk space is above 90%

## Usage

Here's how you can register the check.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;

Health::checks([
    UsedDiskSpaceCheck::new(),
]);
```

### Customizing the thresholds

To customize the usage disk space percentages, you can use `warnWhenUsedSpaceIsAbovePercentage` and `failWhenUsedSpaceIsAbovePercentage`.

```php
UsedDiskSpaceCheck::new()
    ->warnWhenUsedSpaceIsAbovePercentage(60)
    ->failWhenUsedSpaceIsAbovePercentage(80),
```
