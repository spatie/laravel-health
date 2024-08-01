<?php

use Illuminate\Support\Facades\Notification;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\Enums\Status;
use Spatie\Health\Facades\Health;
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
    $notification = (new CheckFailedNotification([]));
    $notification->shouldSend(new Notifiable(), 'mail');
    $mailable = $notification->toMail();

    $html = (string) $mailable->render();

    expect($html)->toBeString();
});

it('can disable warning notifications', function () {
    TestTime::freeze();
    Health::checks([
        $check = FakeUsedDiskSpaceCheck::new()
            ->warnWhenUsedSpaceIsAbovePercentage(10)
            ->failWhenUsedSpaceIsAbovePercentage(50)
            ->fakeDiskUsagePercentage(11)
            ->disableNotificationsOnWarning(),
    ]);

    artisan(RunHealthChecksCommand::class)->assertSuccessful();
    Notification::assertSentTimes(CheckFailedNotification::class, 0);

    $check->fakeDiskUsagePercentage(51);

    artisan(RunHealthChecksCommand::class)->assertSuccessful();
    Notification::assertSentTimes(CheckFailedNotification::class, 1);
});

it('can disable failure notifications', function () {
    TestTime::freeze();
    Health::checks([
        $check = FakeUsedDiskSpaceCheck::new()
            ->warnWhenUsedSpaceIsAbovePercentage(10)
            ->failWhenUsedSpaceIsAbovePercentage(50)
            ->fakeDiskUsagePercentage(11)
            ->disableNotificationsOnFailure(),
    ]);

    artisan(RunHealthChecksCommand::class)->assertSuccessful();
    Notification::assertSentTimes(CheckFailedNotification::class, 1);

    $check->fakeDiskUsagePercentage(51);

    artisan(RunHealthChecksCommand::class)->assertSuccessful();
    Notification::assertSentTimes(CheckFailedNotification::class, 1);
});

it('can change warning throttle time', function () {
    TestTime::freeze();
    Health::checks([
        $check = FakeUsedDiskSpaceCheck::new()
            ->warnWhenUsedSpaceIsAbovePercentage(10)
            ->failWhenUsedSpaceIsAbovePercentage(50)
            ->fakeDiskUsagePercentage(11)
            ->throttleWarningNotificationsFor(15),
    ]);

    artisan(RunHealthChecksCommand::class)->assertSuccessful();
    Notification::assertSentTimes(CheckFailedNotification::class, 1);

    TestTime::addMinutes(15)->subSecond();
    artisan(RunHealthChecksCommand::class)->assertSuccessful();
    Notification::assertSentTimes(CheckFailedNotification::class, 1);

    TestTime::addSecond();
    artisan(RunHealthChecksCommand::class)->assertSuccessful();
    Notification::assertSentTimes(CheckFailedNotification::class, 2);

    TestTime::addSecond();
    $check->fakeDiskUsagePercentage(55);
    artisan(RunHealthChecksCommand::class)->assertSuccessful();
    Notification::assertSentTimes(CheckFailedNotification::class, 3);
});

it('can change failure throttle time', function () {
    TestTime::freeze();
    Health::checks([
        $check = FakeUsedDiskSpaceCheck::new()
            ->warnWhenUsedSpaceIsAbovePercentage(10)
            ->failWhenUsedSpaceIsAbovePercentage(50)
            ->fakeDiskUsagePercentage(55)
            ->throttleFailureNotificationsFor(15),
    ]);

    artisan(RunHealthChecksCommand::class)->assertSuccessful();
    Notification::assertSentTimes(CheckFailedNotification::class, 1);

    TestTime::addMinutes(15)->subSecond();
    artisan(RunHealthChecksCommand::class)->assertSuccessful();
    Notification::assertSentTimes(CheckFailedNotification::class, 1);

    TestTime::addSecond();
    artisan(RunHealthChecksCommand::class)->assertSuccessful();
    Notification::assertSentTimes(CheckFailedNotification::class, 2);

    TestTime::addSecond();
    $check->fakeDiskUsagePercentage(11);
    artisan(RunHealthChecksCommand::class)->assertSuccessful();
    Notification::assertSentTimes(CheckFailedNotification::class, 3);
});

it('can dispatch notifications for crashed check', function () {
    TestTime::freeze();
    Health::checks([
        new class extends Check
        {
            public function run(): Result
            {
                return new Result(Status::crashed(), 'Check Crashed', 'Check Crashed');
            }
        },
    ]);

    artisan(RunHealthChecksCommand::class)->assertSuccessful();
    Notification::assertSentTimes(CheckFailedNotification::class, 1);
});

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
