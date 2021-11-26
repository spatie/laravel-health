---
title: Via mail
weight: 2
---

When a check starts returning warnings or failures, you can get notified via mail.

To do this you must set the `CheckFailedNotification` to use the `slack` channel. This can be done in the config file.

```php
// in config/health.php

'notifications' => [
    Spatie\Health\Notifications\CheckFailedNotification::class => ['slack'],
],
```

In the `slack` key of the `health` config file, you can configure the various Slack settings.

```php
// in config/health.php

'slack' => [
    'webhook_url' => '',

    /*
     * If this is set to null the default channel of the webhook will be used.
     */
    'channel' => null,

    'username' => null,

    'icon' => null,
],
```
