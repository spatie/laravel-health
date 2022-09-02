---
title: Via Slack
weight: 2
---

When a check starts returning warnings or failures, you can get notified via slack.

To use this channel, you must install the Slack notification channel.

```bash
composer require laravel/slack-notification-channel
```

To do this you must set the `CheckFailedNotification` to use the `slack` channel. This can be done in the config file.

```php
// in config/health.php

'notifications' => [
    Spatie\Health\Notifications\CheckFailedNotification::class => ['slack'],
],
```

In the `slack` key of the `health` config file, you can configure the various Slack settings. Set `HEALTH_SLACK_WEBHOOK_URL` env variable with a valid Slack webhook URL. You can learn how to get a webhook URL [in the Slack API docs](https://api.slack.com/messaging/webhooks).

```php
// in config/health.php

'slack' => [
    'webhook_url' => env('HEALTH_SLACK_WEBHOOK_URL', ''),

    /*
     * If this is set to null the default channel of the webhook will be used.
     */
    'channel' => null,

    'username' => null,

    'icon' => null,
],
```
