---
title: Reverb
weight: 18
---

This check will make sure your application can communicate with your [Laravel Reverb](https://reverb.laravel.com) server.

It sends a signed request to the [channels endpoint](https://pusher.com/docs/channels/library_auth_reference/rest-api/#get-channels-fetch-info-for-multiple-channels) of Reverb's HTTP API, using the same client and broadcasting configuration as your application. This verifies that the Reverb server is reachable and accepts a request signed with your application's secret for the configured app ID. The request is read-only: no event is broadcast.

This check requires the `pusher/pusher-php-server` package, which is installed when you set up broadcasting with Reverb.

## Usage

Here's how you can register the check.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\ReverbCheck;

Health::checks([
    ReverbCheck::new(),
]);
```

The check will fail when:

- the broadcasting connection is not configured, or is missing its `app_id`, `key`, `secret` or `options.host`
- the Reverb server can't be reached
- the Reverb server rejects the credentials
- the Reverb server returns any other error or an invalid channel listing

Failure messages do not include raw client exceptions or response bodies, which may contain credentials or signed URLs.

### Customizing the broadcasting connection

By default, the check will use the `reverb` connection defined in `config/broadcasting.php`. You can use the `connectionName()` method to check another connection. This connection must use the `reverb` driver.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\ReverbCheck;

Health::checks([
    ReverbCheck::new()->connectionName('other-reverb'),
]);
```

### Customizing the timeout

By default, the check has a timeout of 1 second.

You can use `timeout()` to set the maximum number of seconds the HTTP request should run for before it fails. The timeout must be at least 1 second.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\ReverbCheck;

Health::checks([
    ReverbCheck::new()->timeout(2),
]);
```

### What this check does not verify

Reverb's HTTP API validates the app ID and request signature, but does not independently validate the app key. An incorrect key can therefore still produce a successful result; this check is not a WebSocket handshake or subscription check.

This check verifies the communication between your application server and Reverb. It doesn't verify that browsers can connect to Reverb, as they usually go through a different path (public host, TLS, reverse proxy). To monitor that, use an external uptime monitor on your public WebSocket host.
