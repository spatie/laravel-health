<?php

use Illuminate\Support\Facades\Notification;
use function Pest\Laravel\artisan;
use Spatie\Health\Commands\RunChecksCommand;
use Spatie\Health\Enums\Status;
use Spatie\Health\Facades\Health;
use Spatie\Health\Models\HealthCheckResultHistoryItem;
use Spatie\Health\Notifications\CheckFailedNotification;
use Spatie\Health\Tests\TestClasses\CrashingCheck;
use Spatie\Health\Tests\TestClasses\FakeUsedDiskSpaceCheck;

beforeEach(function () {
    $this->fakeDiskSpaceCheck = FakeUsedDiskSpaceCheck::new();

    Health::checks([
        $this->fakeDiskSpaceCheck,
    ]);
});

it('can store the ok results in the database', function () {
    artisan(RunChecksCommand::class)->assertSuccessful();

    $historyItems = HealthCheckResultHistoryItem::get();

    expect($historyItems)
        ->toHaveCount(1)
        ->and($historyItems->first())
        ->notification_message->toBeEmpty()
        ->status->toBe(Status::ok()->value)
        ->meta->toBe(['disk_space_used_percentage' => 0]);
});

it('has an option that will not store the results in the database', function () {
    artisan('health:run --do-not-store-results')->assertSuccessful();

    $historyItems = HealthCheckResultHistoryItem::get();

    expect($historyItems)->toHaveCount(0);
});

it('will send a notification when a checks fails', function () {
    Notification::fake();

    $this->fakeDiskSpaceCheck->fakeDiskUsagePercentage(100);
    artisan(RunChecksCommand::class)->assertSuccessful();

    Notification::assertTimesSent(1, CheckFailedNotification::class);
});

it('has an option that will prevent notifications being sent', function () {
    Notification::fake();

    $this->fakeDiskSpaceCheck->fakeDiskUsagePercentage(100);
    artisan('health:run --no-notification')->assertSuccessful();

    Notification::assertTimesSent(0, CheckFailedNotification::class);
});


it('can store the with warnings results in the database', function () {
    $this
        ->fakeDiskSpaceCheck
        ->warnWhenUsedSpaceIsAbovePercentage(50)
        ->fakeDiskUsagePercentage(51);

    artisan(RunChecksCommand::class)->assertSuccessful();

    $historyItems = HealthCheckResultHistoryItem::get();

    expect($historyItems)
        ->toHaveCount(1)
        ->and($historyItems->first())
        ->notification_message->toBe('The disk is almost full (51% used).')
        ->status->toBe(Status::warning()->value)
        ->meta->toBe(['disk_space_used_percentage' => 51]);
});

it('can store the with failures results in the database', function () {
    $this
        ->fakeDiskSpaceCheck
        ->failWhenUsedSpaceIsAbovePercentage(50)
        ->fakeDiskUsagePercentage(51);

    artisan(RunChecksCommand::class)->assertSuccessful();

    $historyItems = HealthCheckResultHistoryItem::get();

    expect($historyItems)
        ->toHaveCount(1)
        ->and($historyItems->first())
        ->notification_message->toBe('The disk is almost full (51% used).')
        ->status->toBe(Status::failed()->value)
        ->meta->toBe(['disk_space_used_percentage' => 51]);
});

it('will still run checks when there is a failing one', function () {
    Health::clearChecks()->checks([
        new CrashingCheck(),
        new FakeUsedDiskSpaceCheck(),
    ]);

    artisan(RunChecksCommand::class)
        ->assertFailed()
        ->expectsOutput('The check named `Crashing` did not complete. An exception was thrown with this message: `This check will always crash`');

    $historyItems = HealthCheckResultHistoryItem::get();

    expect($historyItems)
        ->toHaveCount(2)
        ->and($historyItems[0])
        ->status->toBe(Status::crashed()->value)
        ->and($historyItems[1])
        ->message->toBeEmpty()
        ->status->toBe(Status::ok()->value)
        ->meta->toBe(['disk_space_used_percentage' => 0]);
});
