<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\Facades\Health;
use Spatie\Health\Notifications\CheckRecoveredNotification;
use Spatie\TestTime\TestTime;

use function Pest\Laravel\artisan;

beforeEach(function () {
    Health::clearChecks();
    Notification::fake();
    Cache::clear();
});

it('sends a recovery notification when the throttle key exists', function () {
    // The presence of the throttle key *alone* enables the recovery notification
    markPreviousFailure();
    registerPassingCheck();
    artisan(RunHealthChecksCommand::class)->assertSuccessful();

    Notification::assertSentTimes(CheckRecoveredNotification::class, 1);
});

it('does not send a recovery notification when notifications are disabled', function () {
    config()->set('health.notifications.enabled', false);

    markPreviousFailure();
    registerPassingCheck();
    artisan(RunHealthChecksCommand::class)->assertSuccessful();

    Notification::assertNothingSent();
});

it('does not send a recovery notification when throttle key is missing', function () {
    // No previous failure → no throttle key → no recovery notification
    registerPassingCheck();
    artisan(RunHealthChecksCommand::class)->assertSuccessful();

    Notification::assertNothingSent();
});

it('sends only one recovery notification until throttled again', function () {
    TestTime::freeze();

    markPreviousFailure();

    registerPassingCheck();
    artisan(RunHealthChecksCommand::class)->assertSuccessful();
    Notification::assertSentTimes(CheckRecoveredNotification::class, 1);

    // Immediately run again → throttle key no longer exists → no second notification
    artisan(RunHealthChecksCommand::class)->assertSuccessful();
    Notification::assertSentTimes(CheckRecoveredNotification::class, 1);

    // Simulate next recovery cycle by restoring the throttle key
    markPreviousFailure();

    artisan(RunHealthChecksCommand::class)->assertSuccessful();
    Notification::assertSentTimes(CheckRecoveredNotification::class, 2);
});

test('the recovery notification can be rendered to mail', function () {
    $mailable = (new CheckRecoveredNotification)->toMail();

    $html = (string) $mailable->render();

    expect($html)->toBeString()->not->toBeEmpty();
});

/**
 * Helper: Simulate that a failed notification was sent earlier
 * so the recovery notification can be triggered.
 */
function markPreviousFailure(string $channel = 'mail'): void
{
    $key = config('health.notifications.throttle_notifications_key', 'health:latestNotificationSentAt:') . $channel;
    Cache::put($key, now()->timestamp);
}
