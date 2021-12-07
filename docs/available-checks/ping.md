---
title: Ping
weight: 6
---

This check will send a request to a given URL.  It will report a failure when that URL doesn't respond with a successfull response code within a second.

## Usage

Here's how you can register the check.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\PingCheck;

Health::checks([
    PingCheck::new()->url('https://example.com'),
]);
```
