<?php

use Illuminate\Support\Facades\Notification;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\Facades\Health;
use Spatie\Health\Notifications\CheckFailedNotification;
use Spatie\Health\Tests\TestClasses\FakeUsedDiskSpaceCheck;
use Spatie\TestTime\TestTime;

use function Pest\Laravel\artisan;

beforeEach(function () {
    Notification::fake();
});

it('will not send a notification when none of the checks have a message', function () {
    registerPassingCheck();

    artisan(RunHealthChecksCommand::class)->assertSuccessful();

    Notification::assertNothingSent();
});

it('will send a notification when one of the checks has a message', function () {
    registerFailingCheck();

    artisan(RunHealthChecksCommand::class)->assertSuccessful();

    Notification::assertSentTimes(CheckFailedNotification::class, 1);
});

it('will not send any notifications if the config option is set to false', function () {
    config()->set('health.notifications.enabled', false);

    registerFailingCheck();

    artisan(RunHealthChecksCommand::class)->assertSuccessful();

    Notification::assertSentTimes(CheckFailedNotification::class, 0);
});

it('will only send one failed notification per hour', function () {
    TestTime::freeze();
    registerFailingCheck();

    artisan(RunHealthChecksCommand::class)->assertSuccessful();
    Notification::assertSentTimes(CheckFailedNotification::class, 1);

    TestTime::addHour()->subSecond();
    artisan(RunHealthChecksCommand::class)->assertSuccessful();
    Notification::assertSentTimes(CheckFailedNotification::class, 1);

    TestTime::addSecond();
    artisan(RunHealthChecksCommand::class)->assertSuccessful();
    Notification::assertSentTimes(CheckFailedNotification::class, 2);
});

it('can configure the throttle notifications key', function () {
    TestTime::freeze();
    registerFailingCheck();

    artisan(RunHealthChecksCommand::class)->assertSuccessful();
    Notification::assertSentTimes(CheckFailedNotification::class, 1);

    artisan(RunHealthChecksCommand::class)->assertSuccessful();
    Notification::assertSentTimes(CheckFailedNotification::class, 1);

    config()->set('health.notifications.throttle_notifications_key', 'some-other-key');

    artisan(RunHealthChecksCommand::class)->assertSuccessful();
    Notification::assertSentTimes(CheckFailedNotification::class, 2);
});

test('the notification can be rendered to mail', function () {
    $mailable = (new CheckFailedNotification([]))->toMail();

    $html = (string) $mailable->render();

    expect($html)->toBeString();
});

it('uses the complete Czech translations for both supported locale names', function (string $locale) {
    app()->setLocale($locale);
    app()->setFallbackLocale('en');

    expect(trans('health::notifications'))->toBe([
        'laravel_health' => 'Laravel Health',
        'check_failed_mail_subject' => 'Některé kontroly stavu :application_name selhaly',
        'check_failed_mail_body' => 'Následující kontroly hlásí upozornění a chyby:',
        'check_failed_slack_message' => 'Některé kontroly stavu :application_name selhaly.',
        'health_results' => 'Výsledky',
        'check_results_from' => 'Poslední kontrola',
    ]);

    config()->set('app.name', 'Monitor');

    $mailable = (new CheckFailedNotification([]))->toMail();

    expect($mailable->subject)->toBe('Některé kontroly stavu Monitor selhaly')
        ->and((string) $mailable->render())->toContain('Následující kontroly hlásí upozornění a chyby:');
})->with(['cs', 'cz']);

it('resolves namespaced translations through the configured fallback locale', function (string $fallbackLocale, string $expected) {
    app()->setLocale('unknown');
    app()->setFallbackLocale($fallbackLocale);

    app('translator')->addLines(['notifications.health_results' => 'Application results'], 'unknown');

    expect(trans('notifications.health_results'))->toBe('Application results')
        ->and(trans('health::notifications.health_results'))->toBe($expected)
        ->and(app('translator')->get('health::notifications.health_results', [], null, false))->toBe('health::notifications.health_results')
        ->and(trans('health::notifications.missing'))->toBe('health::notifications.missing');
})->with([
    ['cs', 'Výsledky'],
    ['cz', 'Výsledky'],
    ['en', 'Health Results'],
]);

function registerPassingCheck()
{
    Health::checks([
        FakeUsedDiskSpaceCheck::new()
            ->failWhenUsedSpaceIsAbovePercentage(10)
            ->fakeDiskUsagePercentage(0),
    ]);
}

function registerFailingCheck()
{
    Health::checks([
        FakeUsedDiskSpaceCheck::new()
            ->failWhenUsedSpaceIsAbovePercentage(10)
            ->fakeDiskUsagePercentage(11),
    ]);
}
