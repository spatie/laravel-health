---
title: Queue
weight: 18
---

This check will make sure that queue jobs are running. If the check detects that the queue job is not to run for more than five minutes, it will fail.

This check relies on cache.

## Usage

First, you must register the `QueueCheck`

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\QueueCheck;

Health::checks([
    QueueCheck::new(),
]);
```

Next, you must schedule the `Spatie\Health\Commands\QueueCheckHeartbeatCommand` to run every minute. We recommend putting this command as the very last command in your schedule.

```php
// in app/Console/Kernel.php
use \Spatie\Health\Commands\QueueCheckHeartbeatCommand;

public function schedule(Schedule $schedule) {
    // your other commands

    $schedule->command(QueueCheckHeartbeatCommand::class)->everyMinute();
}
```

### Customize the cache store

This check relies on cache to work. We highly recommend creating a [new cache store](https://laravel.com/docs/8.x/cache#configuration) and pass its name to `useCacheStore`

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\QueueCheck;

Health::checks([
    QueueCheck::new()->useCacheStore('your-custom-store-name'),
]);
```

### Customizing the maximum heart beat age

The `QueueCheckHeartbeatCommand` will write the current timestamp into the cache. The `QueueCheck` will verify that that timestamp is not over 5 minutes.

Should you get too many false positives, you can change the max age of the timestamp by calling `heartbeatMaxAgeInMinutes`.

```php
QueueCheck::new()->heartbeatMaxAgeInMinutes(10),
```
