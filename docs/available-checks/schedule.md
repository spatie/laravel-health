---
title: Schedule
weight: 19
---

This check will make sure the schedule is running. If the check detects that the schedule is not run every minute, it will fail.

This check relies on cache.

## Usage

First, you must register the `ScheduleCheck`

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\ScheduleCheck;

Health::checks([
    ScheduleCheck::new(),
]);
```

Next, you must schedule the `Spatie\Health\Commands\ScheduleCheckHeartbeatCommand` to run every minute. We recommend to put this command as the very last command in your schedule.

```php
// in app/Console/Kernel.php
use \Spatie\Health\Commands\ScheduleCheckHeartbeatCommand;

public function schedule(Schedule $schedule) {
    // your other commands

    $schedule->command(ScheduleCheckHeartbeatCommand::class)->everyMinute();
}
```

### Customize the cache store

This check relies on cache to work. We highly recommend creating a [new cache store](https://laravel.com/docs/8.x/cache#configuration) and pass its name to `useCacheStore`

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\ScheduleCheck;

Health::checks([
    ScheduleCheck::new()->useCacheStore('your-custom-store-name'),
]);
```

### Customizing the maximum heart beat age

The `ScheduleCheckHeartbeatCommand` will write the current timestamp into the cache. The `ScheduleCheck` will verify that that timestamp is not over a minute.

Should you get too many false positives, you can change the max age of the timestamp by calling `heartbeatMaxAgeInMinutes`.

```php
ScheduleCheck::new()->heartbeatMaxAgeInMinutes(2),
```

### Checking individual scheduled tasks

To monitor your individual scheduled tasks, take a look at [our schedule monitor package](https://github.com/spatie/laravel-schedule-monitor).

## Ping Configuration

The check can be configured to ping a URL when the scheduler is running successfully. This is useful for external monitoring services like Oh Dear, Pingdom, Envoyer heartbeats etc.

If a URL is configured, it will automatically be pinged each time the health checks run via the `ScheduleCheckHeartbeatCommand` in your scheduler at the frequency you've configured. NB!!! The URL will only be pinged if the check passes.

The ping is independent of the check's status, so the check may pass but the ping may fail (e.g. the ping URL is malformed or unreachable).

### Simple Setup

The easiest way to set up pinging is through your `.env` file:

```env
SCHEDULE_HEARTBEAT_URL=https://your-monitoring-service.com/ping/abc123
```

When this URL is set, it will automatically be pinged each time the health checks run via the `ScheduleCheckHeartbeatCommand` in your scheduler at the frequency you've configured.

### Advanced Configuration

For more control, you can set the timeout and retry times in your check registration:

```php
Health::checks([
    ScheduleCheck::new()
        ->pingTimeout(5)    // Set timeout in seconds (default: 3)
        ->pingRetryTimes(3) // Set number of retry attempts (default: 1)
]);
```

When a ping fails, it will be logged to your application's log file.
