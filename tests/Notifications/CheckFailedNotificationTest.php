<?php

use Illuminate\Support\Facades\Notification;
use function Pest\Laravel\artisan;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\Facades\Health;
use Spatie\Health\Notifications\CheckFailedNotification;
use Spatie\Health\Tests\TestClasses\FakeUsedDiskSpaceCheck;
use Spatie\TestTime\TestTime;

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

    Notification::assertTimesSent(1, CheckFailedNotification::class);
});


it('will not send any notifications if the config option is set to false', function () {
    config()->set('health.notifications.enabled', false);

    registerFailingCheck();

    artisan(RunHealthChecksCommand::class)->assertSuccessful();

    Notification::assertTimesSent(0, CheckFailedNotification::class);
});

it('will only send one failed notification per hour', function () {
    TestTime::freeze();
    registerFailingCheck();

    artisan(RunHealthChecksCommand::class)->assertSuccessful();
    Notification::assertTimesSent(1, CheckFailedNotification::class);

    TestTime::addHour()->subSecond();
    artisan(RunHealthChecksCommand::class)->assertSuccessful();
    Notification::assertTimesSent(1, CheckFailedNotification::class);

    TestTime::addSecond();
    artisan(RunHealthChecksCommand::class)->assertSuccessful();
    Notification::assertTimesSent(2, CheckFailedNotification::class);
});


test('the notification can be rendered to mail', function () {
    $mailable = (new CheckFailedNotification([]))->toMail();

    $html = (string)$mailable->render();

    expect($html)->toBeString();
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
