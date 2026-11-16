---
title: Via Microsoft Teams
weight: 4
---

When a check starts returning warnings or failures, you can get notified in a Microsoft Teams channel. No extra packages are needed.

First, create a webhook in Teams. In the channel you want notifications in, open **Workflows** and pick the template **"Send webhook alerts to a channel"** (its trigger is "When a Teams webhook request is received"). When you save the workflow, Teams shows a webhook URL. Copy it.

Next, set the `CheckFailedNotification` to use the `teams` channel. You do this in the config file.

```php
// in config/health.php

'notifications' => [
    Spatie\Health\Notifications\CheckFailedNotification::class => ['teams'],
],
```

Then set the `HEALTH_TEAMS_WEBHOOK_URL` env variable to the webhook URL you copied.

```php
// in config/health.php

'teams' => [
    'webhook_url' => env('HEALTH_TEAMS_WEBHOOK_URL', ''),
],
```

The notification is posted as an [Adaptive Card](https://adaptivecards.io). It lists every check that failed or gave a warning, with its status and message.

The legacy Office 365 connector ("Incoming Webhook") URLs are deprecated by Microsoft and are not supported.
