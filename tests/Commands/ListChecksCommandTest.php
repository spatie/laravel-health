<?php

use Spatie\Health\Commands\ListHealthChecksCommand;
use Spatie\Health\Facades\Health;
use Spatie\Health\Tests\TestClasses\FakeUsedDiskSpaceCheck;

use function Pest\Laravel\artisan;

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

it('can use multiple options at once', function () {
    $fakeDiskSpaceCheck = FakeUsedDiskSpaceCheck::new();

    Health::checks([
        $fakeDiskSpaceCheck,
    ]);

    artisan('health:list --fresh --do-not-store-results --no-notification')->assertSuccessful();
});
