---
title: Via Oh Dear
weight: 4
---

In certain scenario's, your application can be in such a bad state, that it can't send any notifications anymore. A possible solution is to let [Oh Dear](https://ohdear.app) monitor your saved check results, and let that service send a notification for you. 

Using this package, you can register a protected endpoint where Oh Dear can read the latest results of the health checks.

You must configure the `ohdear_endpoint_key` in the `health` config file.

```php
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
```

Follow the instructions at the Application Health settings screen at Oh Dear.

## Always sending fresh results to Oh Dear

A common way to use this package, is to schedule the `RunHealthChecksCommand` to run the checks every minute. Depending on when Oh Dear will visit the configured URL, the check results could be a maximum 59 seconds old.

If you always what to send fresh results to Oh Dear, set the `always_send_fresh_results` config option to `true`. So when Oh Dear sends a request `/oh-dear-health-check-results` the checks will be run inside that request and the most fresh results will be sent.

If you always are sending fresh results, then you are not required to store the health results locally. You can use the [in memory result store](/docs/laravel-health/v1/storing-results/not-storing-results) to not store results locally.

