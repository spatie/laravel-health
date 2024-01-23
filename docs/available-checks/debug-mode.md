---
title: Debug mode
weight: 10
---

This check will make sure that debug mode is set to `false`. It will fail when debug mode is `true`.

## Usage

Here's how you can register the check.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\DebugModeCheck;

Health::checks([
    DebugModeCheck::new(),
]);
```
