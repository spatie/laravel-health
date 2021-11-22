<?php

namespace Spatie\Health\Notifications;

use Carbon\Carbon;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Spatie\Health\Support\Result;

class CheckFailedNotification extends Notification
{
    /** @param array<int, Result> $results */
    public function __construct(public array $results)
    {
    }

    /** @return array<int,string> */
    public function via(): array
    {
        /** @var array<int, string> $notificationChannels */
        $notificationChannels = config('health.notifications.notifications.'.static::class);

        return array_filter($notificationChannels);
    }

    public function shouldSend(Notifiable $notifiable, string $channel): bool
    {
        /** @var int $throttleMinutes */
        $throttleMinutes = config('health.notifications.throttle_notifications_for_minutes');

        if ($throttleMinutes === 0) {
            return true;
        }

        $cacheKey = 'health.latestNotificationSentAt';

        /** @var \Illuminate\Cache\CacheManager $cache */
        $cache = app('cache');

        /** @var string $timestamp */
        $timestamp = $cache->get($cacheKey);

        if (! $timestamp) {
            $cache->set('health.latestNotificationSentAt', now()->timestamp);

            return true;
        }

        if (Carbon::createFromTimestamp($timestamp)->addMinutes($throttleMinutes)->isFuture()) {
            return false;
        }

        $cache->set('health.latestNotificationSentAt', now()->timestamp);

        return true;
    }

    public function toMail(): MailMessage
    {
        $mailMessage = (new MailMessage())
            ->error()
            /** @phpstan-ignore-next-line */
            ->from(config('health.notifications.mail.from.address', config('mail.from.address')), config('health.notifications.mail.from.name', config('mail.from.name')))
            ->subject(trans('health::notifications.check_failed_subject', ['application_name' => $this->applicationName()]))
            ->line(trans('health::notifications.check_failed_body', ['application_name' => $this->applicationName()]));
        collect($this->results)
            ->each(function (Result $result) use ($mailMessage) {
                $mailMessage->line("{$result->check->getName()}: {$result->getMessage()}");
            });

        return $mailMessage;
    }

    public function toSlack(): SlackMessage
    {
        return (new SlackMessage())
            ->error()
            /** @phpstan-ignore-next-line */
            ->from(config('health.notifications.slack.username'), config('health.notifications.slack.icon'))
            /** @phpstan-ignore-next-line */
            ->to(config('health.notifications.slack.channel'))
            ->content(trans('health::notifications.check_failed_subject', ['application_name' => $this->applicationName()]));
    }

    public function applicationName(): string
    {
        /** @var string $applicationName */
        $applicationName = config('app.name') ?? config('app.url') ?? 'Laravel application';

        return $applicationName;
    }
}
