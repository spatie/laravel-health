<?php

namespace Spatie\Health\Notifications;

use Carbon\Carbon;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Spatie\Health\Checks\Result;
use Spatie\Health\Enums\Status;

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
        $notificationChannels = config('health.notifications.notifications.' . static::class);

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
            $cache->set($cacheKey, now()->timestamp);

            return true;
        }

        if (Carbon::createFromTimestamp($timestamp)->addMinutes($throttleMinutes)->isFuture()) {
            return false;
        }

        $cache->set($cacheKey, now()->timestamp);

        return true;
    }

    public function toMail(): MailMessage
    {
        return (new MailMessage())
            ->from(config('health.notifications.mail.from.address', config('mail.from.address')), config('health.notifications.mail.from.name', config('mail.from.name')))
            ->subject(trans('health::notifications.check_failed_mail_subject', ['application_name' => $this->applicationName()]))
            ->markdown('health::mail.checkFailedNotification', ['results' => $this->results]);
    }

    public function toSlack(): SlackMessage
    {
        $slackMessage = (new SlackMessage())
            ->error()
            ->from(config('health.notifications.slack.username'), config('health.notifications.slack.icon'))
            ->to(config('health.notifications.slack.channel'))
            ->content(trans('health::notifications.check_failed_slack_message', ['application_name' => $this->applicationName()]));

        foreach ($this->results as $result) {
            $slackMessage->attachment(function (SlackAttachment $attachment) use ($result) {
                $attachment
                    ->color(Status::from($result->status)->getSlackColor())
                    ->title($result->check->getLabel())
                    ->content($result->getNotificationMessage());
            });
        }

        return $slackMessage;
    }

    public function applicationName(): string
    {
        /** @var string $applicationName */
        $applicationName = config('app.name') ?? config('app.url') ?? 'Laravel application';

        return $applicationName;
    }
}
