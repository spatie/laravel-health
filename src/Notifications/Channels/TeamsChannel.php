<?php

namespace Spatie\Health\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class TeamsChannel
{
    public function send(mixed $notifiable, Notification $notification): void
    {
        /** @var string|null $webhookUrl */
        $webhookUrl = $notifiable->routeNotificationFor('teams', $notification);

        if (empty($webhookUrl)) {
            return;
        }

        if (! method_exists($notification, 'toTeams')) {
            return;
        }

        /** @var array<string, mixed> $payload */
        $payload = $notification->toTeams($notifiable);

        Http::post($webhookUrl, $payload)->throw();
    }
}
