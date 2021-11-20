<?php

namespace Spatie\Health\Notifications;

use Carbon\Carbon;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Spatie\Health\Support\Result;

class CheckFailedNotification extends Notification
{
    /** @param array<int, Result> $checks */
    public function __construct(public array $results)
    {
    }

    public function via(): array
    {
        $notificationChannels = config('health.notifications.notifications.'.static::class);

        return array_filter($notificationChannels);
    }

    public function shouldSend($notifiable, $channel)
    {
        $throttleMinutes = config('health.notifications.throttle_notifications_for_minutes');

        if ($throttleMinutes === 0) {
            return true;
        }

        $cacheKey = 'health.latestNotificationSentAt';

        $timestamp = cache()->get($cacheKey);

        if (!$timestamp) {
            cache()->set('health.latestNotificationSentAt', now()->timestamp);

            return true;
        }

        if (Carbon::createFromTimestamp($timestamp)->addMinutes($throttleMinutes)->isFuture()) {
            return false;
        }

        cache()->set('health.latestNotificationSentAt', now()->timestamp);

        return true;
    }

    public function toMail(): MailMessage
    {
        $mailMessage = (new MailMessage())
            ->error()
            ->from(config('health.notifications.mail.from.address', config('mail.from.address')), config('health.notifications.mail.from.name', config('mail.from.name')))
            ->subject(trans('health::notifications.check_failed_subject', ['application_name' => $this->applicationName()]))
            ->line(trans('health::notifications.check_failed_body', ['application_name' => $this->applicationName()]));
        collect($this->results)
            ->each(function (Result $result) use ($mailMessage) {
                $mailMessage->line("{$result->check->name()}: {$result->getMessage()}");
            });

        return $mailMessage;
    }

    public function toSlack(): SlackMessage
    {
        return (new SlackMessage())
            ->error()
            ->from(config('health.notifications.slack.username'), config('health.notifications.slack.icon'))
            ->to(config('health.notifications.slack.channel'))
            ->content(trans('health::notifications.check_failed_subject', ['application_name' => $this->applicationName()]));
    }

    public function applicationName(): string
    {
        return config('app.name') ?? config('app.url') ?? 'Laravel application';
    }
}
