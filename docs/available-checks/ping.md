---
title: Ping
weight: 13
---

This check will send a request to a given URL.  It will report a failure when that URL doesn't respond with a successful response code within a second.

## Usage

Here's how you can register the check.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\PingCheck;

Health::checks([
    PingCheck::new()->url('https://example.com'),
]);
```


### Customizing the timeout

You can use `timeout()` to set the maximum number of seconds the HTTP request should run for before the `PingCheck` fails.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\PingCheck;

Health::checks([
    PingCheck::new()->url('https://example.com')->timeout(2),
]);
```
