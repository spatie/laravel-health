---
title: Ping
weight: 15
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


### Customizing the name

You can use `name()` to change the title of the `PingCheck`. This is useful when you have multiple `PingCheck`s and you want to distinguish them from each other easily.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\PingCheck;

Health::checks([
    PingCheck::new()->url('https://example.com')->name('Example'),
    PingCheck::new()->url('https://spatie.be')->name('Spatie'),
]);
```


### Customizing the retry times

You can use `retryTimes()` to set the number of times to retry the `PingCheck` before failing.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\PingCheck;

Health::checks([
    PingCheck::new()->url('https://example.com')->retryTimes(3),
]);
```
