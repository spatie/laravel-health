<?php

use Illuminate\Support\Facades\Notification;
use Spatie\Health\Commands\RunChecksCommand;
use Spatie\Health\Facades\Health;
use Spatie\Health\Notifications\CheckFailedNotification;
use Spatie\Health\Tests\TestClasses\FakeDiskSpaceCheck;
use function Pest\Laravel\artisan;

it('will not send a notification when none of the checks have a message', function() {
    Notification::fake();

    registerPassingCheck();

    artisan(RunChecksCommand::class)->assertSuccessful();

    Notification::assertNothingSent();
});

it('will send a notification when one of the checks has a message', function() {
    Notification::fake();

    registerFailingCheck();

    artisan(RunChecksCommand::class)->assertSuccessful();

    Notification::assertTimesSent(1, CheckFailedNotification::class);
});

function registerPassingCheck()
{
    Health::checks([
        FakeDiskSpaceCheck::new()
            ->errorWhenFreeSpaceIsAbovePercentage(10)
            ->fakeDiskUsagePercentage(0),
    ]);
}

function registerFailingCheck()
{
    Health::checks([
        FakeDiskSpaceCheck::new()
            ->errorWhenFreeSpaceIsAbovePercentage(10)
            ->fakeDiskUsagePercentage(11),
    ]);
}
