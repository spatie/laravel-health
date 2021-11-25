<?php

use Illuminate\Support\Facades\Notification;
use Spatie\TestTime\TestTime;
use function Pest\Laravel\artisan;
use Spatie\Health\Commands\RunChecksCommand;
use Spatie\Health\Facades\Health;
use Spatie\Health\Notifications\CheckFailedNotification;
use Spatie\Health\Tests\TestClasses\FakeUsedDiskSpaceCheck;

beforeEach(function () {
    Notification::fake();
});

it('will not send a notification when none of the checks have a message', function () {
    registerPassingCheck();

    artisan(RunChecksCommand::class)->assertSuccessful();

    Notification::assertNothingSent();
});

it('will send a notification when one of the checks has a message', function () {
    registerFailingCheck();

    artisan(RunChecksCommand::class)->assertSuccessful();

    Notification::assertTimesSent(1, CheckFailedNotification::class);
});

it('will only send one failed notification per hour', function () {
    TestTime::freeze();
    registerFailingCheck();

    artisan(RunChecksCommand::class)->assertSuccessful();
    Notification::assertTimesSent(1, CheckFailedNotification::class);

    TestTime::addHour()->subSecond();
    artisan(RunChecksCommand::class)->assertSuccessful();
    Notification::assertTimesSent(1, CheckFailedNotification::class);

    TestTime::addSecond();
    artisan(RunChecksCommand::class)->assertSuccessful();
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
