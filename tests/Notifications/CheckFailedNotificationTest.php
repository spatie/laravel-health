<?php

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Spatie\Health\Checks\Result;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\Enums\Status;
use Spatie\Health\Facades\Health;
use Spatie\Health\Notifications\Channels\TeamsChannel;
use Spatie\Health\Notifications\CheckFailedNotification;
use Spatie\Health\Notifications\Notifiable;
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

it('maps the teams channel to the teams channel class', function () {
    config()->set('health.notifications.notifications.'.CheckFailedNotification::class, ['mail', 'teams']);

    expect((new CheckFailedNotification([]))->via())->toBe(['mail', TeamsChannel::class]);
});

test('the notification can be rendered to a teams adaptive card', function (Status $status, string $color) {
    config()->set('app.name', 'Monitor');

    $result = (new Result($status, 'Disk is almost full'))
        ->shortSummary('95%')
        ->check(FakeUsedDiskSpaceCheck::new());

    $payload = (new CheckFailedNotification([$result]))->toTeams();

    expect($payload['type'])->toBe('message');

    $attachment = $payload['attachments'][0];
    expect($attachment['contentType'])->toBe('application/vnd.microsoft.card.adaptive');

    $body = $attachment['content']['body'];
    expect($body)->toHaveCount(2)
        ->and($body[0]['text'])->toBe('Some of the health checks on Monitor have failed.')
        ->and($body[1]['items'][0]['text'])->toContain('95%')
        ->and($body[1]['items'][0]['color'])->toBe($color)
        ->and($body[1]['items'][1]['text'])->toBe('Disk is almost full');
})->with([
    [Status::warning(), 'Warning'],
    [Status::failed(), 'Attention'],
    [Status::crashed(), 'Attention'],
]);

it('delivers health check failures to teams and throttles subsequent notifications', function () {
    Notification::swap(new ChannelManager(app()));
    Http::fake();
    TestTime::freeze();
    config()->set('health.notifications.notifications.'.CheckFailedNotification::class, ['teams']);
    config()->set('health.notifications.teams.webhook_url', 'https://example.com/teams-webhook');

    registerFailingCheck();

    artisan(RunHealthChecksCommand::class)->assertSuccessful();

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://example.com/teams-webhook'
            && $request['type'] === 'message'
            && $request['attachments'][0]['content']['type'] === 'AdaptiveCard'
            && $request['attachments'][0]['content']['body'][1]['items'][1]['text'] === 'The disk is almost full (11% used).';
    });

    artisan(RunHealthChecksCommand::class)->assertSuccessful();
    Http::assertSentCount(1);

    TestTime::addHour();
    artisan(RunHealthChecksCommand::class)->assertSuccessful();
    Http::assertSentCount(2);
});

it('does not post to teams when no webhook url is configured', function (?string $webhookUrl) {
    Http::fake();
    config()->set('health.notifications.teams.webhook_url', $webhookUrl);

    (new TeamsChannel)->send(new Notifiable, new CheckFailedNotification([]));

    Http::assertNothingSent();
})->with(['', null]);

it('reports a rejected teams webhook request', function (int $status) {
    Http::fake(['https://example.com/teams-webhook' => Http::response('Rejected', $status)]);
    config()->set('health.notifications.teams.webhook_url', 'https://example.com/teams-webhook');

    expect(fn () => (new TeamsChannel)->send(new Notifiable, new CheckFailedNotification([])))
        ->toThrow(RequestException::class);
})->with([400, 429, 500]);

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
