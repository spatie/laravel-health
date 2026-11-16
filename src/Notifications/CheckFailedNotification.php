<?php

namespace Spatie\Health\Notifications;

use Carbon\Carbon;
use Illuminate\Cache\CacheManager;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Spatie\Health\Checks\Result;
use Spatie\Health\Enums\Status;
use Spatie\Health\Notifications\Channels\TeamsChannel;

class CheckFailedNotification extends Notification
{
    /** @param  array<int, Result>  $results */
    public function __construct(public array $results) {}

    /** @return array<int,string> */
    public function via(): array
    {
        /** @var array<int, string> $notificationChannels */
        $notificationChannels = config('health.notifications.notifications.'.static::class);

        return array_map(
            fn (string $channel) => $channel === 'teams' ? TeamsChannel::class : $channel,
            array_filter($notificationChannels),
        );
    }

    public function shouldSend(Notifiable $notifiable, string $channel): bool
    {
        if (! config('health.notifications.enabled')) {
            return false;
        }

        /** @var int $throttleMinutes */
        $throttleMinutes = config('health.notifications.throttle_notifications_for_minutes');

        if ($throttleMinutes === 0) {
            return true;
        }

        $cacheKey = config('health.notifications.throttle_notifications_key', 'health:latestNotificationSentAt:').$channel;

        /** @var CacheManager $cache */
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
        return (new MailMessage)
            ->from(config('health.notifications.mail.from.address', config('mail.from.address')), config('health.notifications.mail.from.name', config('mail.from.name')))
            ->subject(trans('health::notifications.check_failed_mail_subject', $this->transParameters()))
            ->markdown('health::mail.checkFailedNotification', ['results' => $this->results]);
    }

    public function toSlack(): SlackMessage
    {
        $slackMessage = (new SlackMessage)
            ->error()
            ->from(config('health.notifications.slack.username'), config('health.notifications.slack.icon'))
            ->to(config('health.notifications.slack.channel'))
            ->content(trans('health::notifications.check_failed_slack_message', $this->transParameters()));

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

    /**
     * Builds a message containing an Adaptive Card, as expected by
     * Microsoft Teams "When a Teams webhook request is received" workflows.
     *
     * @return array<string, mixed>
     */
    public function toTeams(): array
    {
        $body = [
            [
                'type' => 'TextBlock',
                'text' => trans('health::notifications.check_failed_teams_message', $this->transParameters()),
                'weight' => 'Bolder',
                'size' => 'Medium',
                'wrap' => true,
            ],
        ];

        foreach ($this->results as $result) {
            $body[] = [
                'type' => 'Container',
                'separator' => true,
                'items' => [
                    [
                        'type' => 'TextBlock',
                        'text' => $result->check->getLabel().' — '.$result->getShortSummary(),
                        'weight' => 'Bolder',
                        'color' => Status::from($result->status)->getTeamsColor(),
                        'wrap' => true,
                    ],
                    [
                        'type' => 'TextBlock',
                        'text' => $result->getNotificationMessage(),
                        'wrap' => true,
                        'spacing' => 'Small',
                    ],
                ],
            ];
        }

        return [
            'type' => 'message',
            'attachments' => [
                [
                    'contentType' => 'application/vnd.microsoft.card.adaptive',
                    'contentUrl' => null,
                    'content' => [
                        '$schema' => 'http://adaptivecards.io/schemas/adaptive-card.json',
                        'type' => 'AdaptiveCard',
                        'version' => '1.4',
                        'msteams' => ['width' => 'Full'],
                        'body' => $body,
                    ],
                ],
            ],
        ];
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
