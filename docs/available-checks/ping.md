---
title: Ping
weight: 15
---

This check will send a request to a given URL.  It will report a failure when that URL doesn't respond with a successful response code within a second.

This check relies on cache.

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

### Fail after minutes

By default, the check will immediately report a failure when the URL is unreachable. You can use `failAfterMinutes()` to delay the failure status. When this option is set, the check will first report a **warning** status when the URL becomes unreachable. Only if the URL remains unreachable for the specified number of minutes, the check will transition to a **failed** status.

This is useful to avoid false alarms caused by temporary network issues or brief service interruptions.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\PingCheck;

Health::checks([
    PingCheck::new()
        ->url('https://example.com')
        ->failAfterMinutes(5),
]);
```

In this example, the check will return a warning for the first 5 minutes of unreachability, and only after that will it report a failure.

### Customize the cache store

The `failAfterMinutes` feature relies on cache to work. It will write a timestamp of the first failure into the cache that will be verified on subsequent checks. You can use `useCacheStore()` to specify a different cache store.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\PingCheck;

Health::checks([
    PingCheck::new()
        ->url('https://example.com')
        ->failAfterMinutes(5)
        ->useCacheStore('your-custom-store-name'),
]);
```

Optionally, you can use `cacheKeyPrefix()` to customize the prefix used for the cache key. The default prefix is `health:checks:ping`.

```php
PingCheck::new()
    ->url('https://example.com')
    ->failAfterMinutes(5)
    ->cacheKeyPrefix('my-app:health:ping'),
```
