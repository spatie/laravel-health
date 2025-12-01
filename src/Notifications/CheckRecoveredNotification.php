<?php

namespace Spatie\Health\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class CheckRecoveredNotification extends Notification
{
    /** @return array<int,string> */
    public function via(): array
    {
        /** @var array<int, string> $notificationChannels */
        $notificationChannels = config('health.notifications.channels.recovered')
            // for config backwards-compatibility
            ?? config('health.notifications.notifications.'.static::class);

        return array_filter($notificationChannels);
    }

    public function shouldSend(Notifiable $notifiable, string $channel): bool
    {
        if (! config('health.notifications.enabled')) {
            return false;
        }

        $cacheKey = config('health.notifications.throttle_notifications_key', 'health:latestNotificationSentAt:').$channel;

        /** @var \Illuminate\Cache\CacheManager $cache */
        $cache = cache();

        // If the cache key existed, we assume there were previous failing checks,
        // and this recovery notification should be sent.
        return (bool) $cache->pull($cacheKey);
    }

    public function toMail(): MailMessage
    {
        return (new MailMessage)
            ->from(
                config('health.notifications.mail.from.address', config('mail.from.address')),
                config('health.notifications.mail.from.name', config('mail.from.name'))
            )
            ->subject(trans('health::notifications.check_recovered_mail_subject', $this->transParameters()))
            ->markdown('health::mail.checkRecoveredNotification');
    }

    public function toSlack(): SlackMessage
    {
        $slackMessage = (new SlackMessage)
            ->error()
            ->from(config('health.notifications.slack.username'), config('health.notifications.slack.icon'))
            ->to(config('health.notifications.slack.channel'))
            ->content(trans('health::notifications.check_recovered_slack_message', $this->transParameters()));

        return $slackMessage;
    }

    /**
     * @return array<string, string>
     */
    public function transParameters(): array
    {
        return [
            'application_name' => config('app.name') ?? config('app.url') ?? 'Laravel application',
            'env_name' => app()->environment(),
        ];
    }
}
