---
title: Installation & setup
weight: 4
---

You can install the package via composer:

```bash
composer require spatie/laravel-health
```

## Publishing the config file

Optionally, you can publish the `health` config file with this command.

```bash
php artisan vendor:publish --tag="health-config"
```

This is the content of the published config file:

```php
return [

    /*
     * A result store is responsible for saving the results of the checks. The
     * `EloquentHealthResultStore` will save results in the database. You
     * can use multiple stores at the same time.
     */
    'result_stores' => [
        Spatie\Health\ResultStores\EloquentHealthResultStore::class => [
            'model' => Spatie\Health\Models\HealthCheckResultHistoryItem::class,
            'keep_history_for_days' => 5,
        ],

        /*
        Spatie\Health\ResultStores\CacheHealthResultStore::class => [
            'store' => 'file',
        ],

        Spatie\Health\ResultStores\JsonFileHealthResultStore::class => [
            'disk' => 's3',
            'path' => 'health.json',
        ],

        Spatie\Health\ResultStores\InMemoryHealthResultStore::class,
        */
    ],

    /*
     * You can get notified when specific events occur. Out of the box you can use 'mail' and 'slack'.
     * For Slack you need to install laravel/slack-notification-channel.
     */
    'notifications' => [
        /*
         * Notifications will only get sent if this option is set to `true`.
         */
        'enabled' => true,

        'notifications' => [
            Spatie\Health\Notifications\CheckFailedNotification::class => ['mail'],
        ],

        /*
         * Here you can specify the notifiable to which the notifications should be sent. The default
         * notifiable will use the variables specified in this config file.
         */
        'notifiable' => Spatie\Health\Notifications\Notifiable::class,

        /*
         * When checks start failing, you could potentially end up getting
         * a notification every minute.
         *
         * With this setting, notifications are throttled. By default, you'll
         * only get one notification per hour.
         */
        'throttle_notifications_for_minutes' => 60,

        'mail' => [
            'to' => 'your@example.com',

            'from' => [
                'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
                'name' => env('MAIL_FROM_NAME', 'Example'),
            ],
        ],

        'slack' => [
            'webhook_url' => env('HEALTH_SLACK_WEBHOOK_URL', ''),

            /*
             * If this is set to null the default channel of the webhook will be used.
             */
            'channel' => null,

            'username' => null,

            'icon' => null,
        ],
    ],

    /*
     * You can let Oh Dear monitor the results of all health checks. This way, you'll
     * get notified of any problems even if your application goes totally down. Via
     * Oh Dear, you can also have access to more advanced notification options.
     */
    'oh_dear_endpoint' => [
        'enabled' => false,

        /*
         * When this option is enabled, the checks will run before sending a response.
         * Otherwise, we'll send the results from the last time the checks have run.
         */
        'always_send_fresh_results' => true,

        /*
         * The secret that is displayed at the Application Health settings at Oh Dear.
         */
        'secret' => env('OH_DEAR_HEALTH_CHECK_SECRET'),

        /*
         * The URL that should be configured in the Application health settings at Oh Dear.
         */
        'url' => '/oh-dear-health-check-results',
    ],

    /*
     * You can set a theme for the local status page
     *
     * - light: light mode
     * - dark: dark mode
     */
    'theme' => 'light',
    
    /*
     * When enabled,  completed `HealthQueueJob`s will be displayed 
     * in Horizon's silenced jobs screen.
     */
    'silence_health_queue_job' => true,
];
```

## Migrating the database

This package can store health check results [in various ways](https://spatie.be/docs/laravel-health/v1/storing-results/general). When using the `EloquentHealthResultStore` the check results will be stored in the database. To create the `health_check_result_history_items` table, you must create and run the migration.

```bash
php artisan vendor:publish --tag="health-migrations"
php artisan migrate
```

These steps are not necessary when using the `JsonFileResultStore`.

## Running the checks by scheduling a command

If you want to let your application send notifications when something is wrong, you should schedule the `RunHealthChecksCommand` to run every minute.

```php
// in route/console.php
use Illuminate\Support\Facades\Schedule;

Schedule::command(\Spatie\Health\Commands\RunHealthChecksCommand::class)->everyMinute();
```

## Running the checks by sending HTTP requests

If you don't want to let your application send notification, but let a service like Oh Dear monitor the health of your app, you can trigger a run of all health checks by visiting the [HTTP endpoint](https://spatie.be/docs/laravel-health/v1/viewing-results/on-a-webpage) or [JSON endpoint](https://spatie.be/docs/laravel-health/v1/viewing-results/as-json) and use the `?fresh` parameter in the URL.
