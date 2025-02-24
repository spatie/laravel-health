---
title: Horizon
weight: 13
---

This check will make sure Horizon is running. It will report a warning when Horizon is paused, and a failure when Horizon is not running.

## Usage

Here's how you can register the check.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\HorizonCheck;

Health::checks([
    HorizonCheck::new(),
]);
```

## Ping Configuration

The check can be configured to ping a URL when Horizon is running successfully. This is useful for external monitoring services like Oh Dear, Pingdom, Envoyer heartbeats etc.

If a URL is configured, it will automatically be pinged each time the health checks run via the `RunHealthChecksCommand` in your scheduler at the frequency you've configured. NB!!! The URL will only be pinged if the check passes.

The ping is independent of the check's status, so the check may pass but the ping may fail (e.g. the ping URL is malformed or unreachable).

### Setting the Heartbeat URL

You can set the heartbeat URL in two ways:

1. Recommendation: Through your `.env` file (will be used as fallback if no URL is explicitly set):
```env
HORIZON_HEARTBEAT_URL=https://your-monitoring-service.com/ping/abc123
```

Then the Horizon check can be used like this:
```php
Health::checks([
    HorizonCheck::new(),
]);
```

2. Directly in your check registration:
```php
Health::checks([
    HorizonCheck::new()
        ->heartbeatUrl('https://your-monitoring-service.com/ping/abc123'),
]);
```

When this URL is set in either of the methods above, it will automatically be pinged each time the health checks run via the `RunHealthChecksCommand` in your scheduler at the frequency you've configured.

### Advanced Configuration

For more control, you can set the timeout and retry times in your check registration:

```php
Health::checks([
    HorizonCheck::new()
        ->pingTimeout(5)    // Set timeout in seconds (default: 3)
        ->pingRetryTimes(3) // Set number of retry attempts (default: 1)
]);
```

When a ping fails, it will be logged to your application's log file.
