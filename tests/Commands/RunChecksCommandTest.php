<?php

use function Pest\Laravel\artisan;
use Spatie\Health\Commands\RunChecksCommand;
use Spatie\Health\Enums\Status;
use Spatie\Health\Facades\Health;
use Spatie\Health\Models\CheckResultHistoryItem;
use Spatie\Health\Tests\TestClasses\CrashingCheck;
use Spatie\Health\Tests\TestClasses\FakeDiskSpaceCheck;

beforeEach(function () {
    $this->fakeDiskSpaceCheck = FakeDiskSpaceCheck::new();

    Health::checks([
        $this->fakeDiskSpaceCheck,
    ]);
});

it('can store the ok results in the database', function () {
    artisan(RunChecksCommand::class)->assertSuccessful();

    $historyItems = CheckResultHistoryItem::get();

    expect($historyItems)
        ->toHaveCount(1)
        ->and($historyItems->first())
        ->message->toBeEmpty()
        ->status->toBe(Status::ok()->value)
        ->meta->toBe(['disk_space_used_percentage' => 0]);
});

it('can store the with warnings results in the database', function () {
    $this
        ->fakeDiskSpaceCheck
        ->warnWhenUsedSpaceIsAbovePercentage(50)
        ->fakeDiskUsagePercentage(51);

    artisan(RunChecksCommand::class)->assertSuccessful();

    $historyItems = CheckResultHistoryItem::get();

    expect($historyItems)
        ->toHaveCount(1)
        ->and($historyItems->first())
        ->message->toBe('The disk is almost full: (51 % used)')
        ->status->toBe(Status::warning()->value)
        ->meta->toBe(['disk_space_used_percentage' => 51]);
});

it('can store the with failures results in the database', function () {
    $this
        ->fakeDiskSpaceCheck
        ->failWhenUsedSpaceIsAbovePercentage(50)
        ->fakeDiskUsagePercentage(51);

    artisan(RunChecksCommand::class)->assertSuccessful();

    $historyItems = CheckResultHistoryItem::get();

    expect($historyItems)
        ->toHaveCount(1)
        ->and($historyItems->first())
        ->message->toBe('The disk is almost full: (51 % used)')
        ->status->toBe(Status::failed()->value)
        ->meta->toBe(['disk_space_used_percentage' => 51]);
});

it('will still run checks when there is a failing one', function () {
    Health::clearChecks()->checks([
        new CrashingCheck(),
        new FakeDiskSpaceCheck(),
    ]);

    artisan(RunChecksCommand::class)
        ->assertFailed()
        ->expectsOutput('The check named `Crashing` did not complete. An exception was thrown with this message: `This check will always crash`');

    $historyItems = CheckResultHistoryItem::get();

    expect($historyItems)
        ->toHaveCount(2)
        ->and($historyItems[0])
        ->status->toBe(Status::crashed()->value)
        ->and($historyItems[1])
        ->message->toBeEmpty()
        ->status->toBe(Status::ok()->value)
        ->meta->toBe(['disk_space_used_percentage' => 0]);
});
