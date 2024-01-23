---
title: Queue
weight: 16
---

This check will make sure that queued jobs are running. This check works by dispatching a test job (this will be done via a scheduled command), and verify if that test job is handled on time.

This check relies on cache.

## Usage

First, you must schedule the `Spatie\Health\Commands\DispatchQueueCheckJobsCommand` to run every minute. This command will dispatch a very light job on the queue you wish to monitor.

```php
// in app/Console/Kernel.php
use \Spatie\Health\Commands\DispatchQueueCheckJobsCommand;

protected function schedule(Schedule $schedule) {
    // your other commands

    $schedule->command(DispatchQueueCheckJobsCommand::class)->everyMinute();
}
```

Next, you must register a `QueueCheck`. When providing no options, this check will monitor the `default` queue, and will fail if the job dispatched by the `DispatchQueueCheckJobsCommand` isn't handled within 5 minutes.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\QueueCheck;

Health::checks([
    QueueCheck::new(),
]);
```

You can monitor a different queue, by tacking on `onQueue`.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\QueueCheck;

Health::checks([
    QueueCheck::new()->onQueue('email'),
]);
```

The `onQueue` method can accept multiple queues.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\QueueCheck;

Health::checks([
    QueueCheck::new()->onQueue(['email', 'payment']),
]);
```

### Customizing job time

By default, the `QueueCheck` will fail when the job dispatched by `DispatchQueueCheckJobsCommand` isn't handled within 5 minutes. You can customize the amount of minutes using the `failWhenHealthJobTakesLongerThanMinutes` method.

```php
QueueCheck::new()->failWhenHealthJobTakesLongerThanMinutes(10),
```

### Customize the cache store

This queue check relies on cache to work. The test job dispatched by `DispatchQueueCheckJobsCommand` will write a timestamp in that cache that will be verified by `QueueCheck`.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\QueueCheck;

Health::checks([
    QueueCheck::new()->useCacheStore('your-custom-store-name'),
]);
```


