---
title: Via Microsoft Teams
weight: 2
---

When a check starts returning warnings or failures, you can get notified via Microsoft Teams.

To use this channel, you must install the Microsoft Teams notification channel.

```bash
composer require laravel-notification-channels/microsoft-teams
```

To do this you must set the `CheckFailedNotification` to use the `microsoftTeams` channel. This can be done in the config file.

```php
// in config/health.php

'notifications' => [
    Spatie\Health\Notifications\CheckFailedNotification::class => ['microsoftTeams'],
],
```

In the `microsoft_teams` key of the `health` config file, you can configure the webhook url for Teams. Set `HEALTH_TEAMS_WEBHOOK_URL` env variable with a valid Microsoft Teams webhook URL. You can learn how to get a webhook URL in the [Microsoft Teams Developer documentation](https://learn.microsoft.com/en-gb/microsoftteams/platform/webhooks-and-connectors/how-to/add-incoming-webhook).

```php
// in config/health.php

'microsoft_teams' => [
    'webhook_url' => env('HEALTH_TEAMS_WEBHOOK_URL', ''),
],
```
