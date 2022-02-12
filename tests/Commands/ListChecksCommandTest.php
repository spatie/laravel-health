<?php

use function Pest\Laravel\artisan;
use Spatie\Health\Commands\ListHealthChecksCommand;
use Spatie\Health\Facades\Health;
use Spatie\Health\Tests\TestClasses\FakeUsedDiskSpaceCheck;

it('thrown no exceptions with no checks registered', function () {
    artisan(ListHealthChecksCommand::class)->assertSuccessful();
});

it('thrown no exceptions with a check registered', function () {
    Health::checks([
        FakeUsedDiskSpaceCheck::new(),
    ]);

    artisan(ListHealthChecksCommand::class, ['--fresh' => true])->assertSuccessful();
});

it('has an option that will let the command fail when a check fails', function () {
    $fakeDiskSpaceCheck = FakeUsedDiskSpaceCheck::new();

    Health::checks([
        $fakeDiskSpaceCheck,
    ]);

    $fakeDiskSpaceCheck->fakeDiskUsagePercentage(0);
    artisan('health:list')->assertSuccessful();
    artisan('health:list --fail-command-on-failing-check')->assertSuccessful();

    $fakeDiskSpaceCheck->fakeDiskUsagePercentage(100);

    artisan('health:check')->assertSuccessful();
    artisan('health:list')->assertSuccessful();
    artisan('health:list --fail-command-on-failing-check')->assertFailed();
});
