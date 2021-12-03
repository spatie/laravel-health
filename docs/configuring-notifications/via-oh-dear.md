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
