<?php

namespace Spatie\Health\Notifications;

use Illuminate\Notifications\Notifiable as NotifiableTrait;

class Notifiable
{
    use NotifiableTrait;

    /** @return string|array<int, string> */
    public function routeNotificationForMail(): string|array
    {
        return config('health.notifications.mail.to');
    }

    public function routeNotificationForSlack(): string
    {
        return config('health.notifications.slack.webhook_url');
    }

    public function getKey(): int
    {
        return 1;
    }
}
