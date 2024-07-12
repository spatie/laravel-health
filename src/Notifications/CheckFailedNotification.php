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
    /** @param  array<int, array<int, Result>>  $results */
    protected array $resultsForNotificationByChannel = [];

    /** @param  array<int, Result>  $results */
    public function __construct(public array $results) {}

    /** @return array<int,string> */
    public function via(): array
    {
        /** @var array<int, string> $notificationChannels */
        $notificationChannels = config('health.notifications.notifications.'.static::class);

        return array_filter($notificationChannels);
    }

    public function shouldSend(Notifiable $notifiable, string $channel): bool
    {
        if (! config('health.notifications.enabled')) {
            return false;
        }

        $filteredResults = $this->filterResultsForNotificationChannel($channel);
        $this->resultsForNotificationByChannel[$channel] = $filteredResults;

        if (count($filteredResults) === 0) {
            return false;
        }

        // If we have any checks that have custom throttle times set that have already been locked
        $hasCustomThrottleCheck = array_reduce($filteredResults, function (bool $a, Result $result) {
            return $a || ($result->check->getThrottleConfiguration()[$result->status->value]['minutes'] ?? null) != null;
        }, false);

        if ($hasCustomThrottleCheck) {
            return true;
        }

        /** @var int $defaultThrottleMinutes */
        $defaultThrottleMinutes = config('health.notifications.throttle_notifications_for_minutes');
        $defaultCacheKey = config('health.notifications.throttle_notifications_key', 'health:latestNotificationSentAt:').$channel;

        return $this->canAcquireLock($defaultCacheKey, $defaultThrottleMinutes);
    }

    public function canAcquireLock(string $cacheKey, int $throttleMinutes): bool
    {
        if ($throttleMinutes === 0) {
            return true;
        }

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
        return (new MailMessage)
            ->from(config('health.notifications.mail.from.address', config('mail.from.address')), config('health.notifications.mail.from.name', config('mail.from.name')))
            ->subject(trans('health::notifications.check_failed_mail_subject', $this->transParameters()))
            ->markdown('health::mail.checkFailedNotification', ['results' => $this->getCheckResults('mail')]);
    }

    public function toSlack(): SlackMessage
    {
        $slackMessage = (new SlackMessage)
            ->error()
            ->from(config('health.notifications.slack.username'), config('health.notifications.slack.icon'))
            ->to(config('health.notifications.slack.channel'))
            ->content(trans('health::notifications.check_failed_slack_message', $this->transParameters()));

        foreach ($this->getCheckResults('slack') as $result) {
            $slackMessage->attachment(function (SlackAttachment $attachment) use ($result) {
                $attachment
                    ->color(Status::from($result->status)->getSlackColor())
                    ->title($result->check->getLabel())
                    ->content($result->getNotificationMessage());
            });
        }

        return $slackMessage;
    }

    public function getCheckResults(string $channel)
    {
        return $this->resultsForNotificationByChannel[$channel];
    }

    protected function filterResultsForNotificationChannel(string $channel): array
    {
        return array_filter($this->results, function ($result) use ($channel) {
            if (! array_key_exists($result->status->value, $result->check->getThrottleConfiguration())) {
                return true;
            }

            $throttleConfig = $result->check->getThrottleConfiguration()[$result->status->value];

            if ($throttleConfig['enabled'] === false) {
                return false;
            }

            $checkCacheKey = implode(':', [
                trim(config('health.notifications.throttle_notifications_key', 'health:latestNotificationSentAt'), ':'),
                $channel,
                get_class($result->check),
            ]);

            if ($throttleConfig['minutes'] != null) {
                if (! $this->canAcquireLock($checkCacheKey, $throttleConfig['minutes'])) {
                    return false;
                }
            }

            return true;
        });
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
