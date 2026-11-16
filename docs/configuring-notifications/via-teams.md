---
title: Via Microsoft Teams
weight: 4
---

When a check starts returning warnings or failures, you can get notified in a Microsoft Teams channel. No extra packages are needed.

First, create a webhook in Teams. In the channel you want notifications in, open **Workflows** and pick the template **"Send webhook alerts to a channel"** (its trigger is "When a Teams webhook request is received"). When you save the workflow, Teams shows a webhook URL. Copy it.

Set the trigger's authentication type to **Anyone**. This channel sends requests without a Microsoft Entra token, so tenant-restricted triggers are not supported. Treat the webhook URL as a secret: anyone with the URL can trigger the workflow.

Next, set `CheckFailedNotification` to use the `teams` channel in `config/health.php`. Both the notification channels and the Teams settings belong inside the outer `notifications` array:

```php
// in config/health.php

'notifications' => [
    // ...
    'notifications' => [
        Spatie\Health\Notifications\CheckFailedNotification::class => ['teams'],
    ],

    'teams' => [
        'webhook_url' => env('HEALTH_TEAMS_WEBHOOK_URL', ''),
    ],
],
```

Then set the `HEALTH_TEAMS_WEBHOOK_URL` environment variable to the webhook URL you copied.

The notification is posted as an [Adaptive Card](https://adaptivecards.io). It lists every check that failed or gave a warning, with its status and message.

The legacy Office 365 connector ("Incoming Webhook") URLs are deprecated by Microsoft and are not supported.
