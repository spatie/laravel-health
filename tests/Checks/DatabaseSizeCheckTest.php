<?php

use Illuminate\Support\Facades\Notification;
use Spatie\Health\Checks\Checks\DatabaseSizeCheck;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\Enums\Status;
use Spatie\Health\Facades\Health;
use Spatie\Health\Tests\TestClasses\FakeDatabaseSizeCheck;

use function Pest\Laravel\artisan;

it('will determine that database size is ok if it does not cross the maximum', function () {
    $result = DatabaseSizeCheck::new()
        ->connectionName('mysql')
        ->failWhenSizeAboveGb(50)
        ->run();

    expect($result->status)->toBe(Status::ok());
});

it('will determine that database size is not ok if it does cross the maximum', function () {
    $result = DatabaseSizeCheck::new()
        ->connectionName('mysql')
        ->failWhenSizeAboveGb(0)
        ->run();

    expect($result->status)->toBe(Status::failed());
});

it('should not send a notification on a successful check', function () {
    Notification::fake();

    registerPassingDatabaseSizeCheck();

    artisan(RunHealthChecksCommand::class)->assertSuccessful();

    Notification::assertNothingSent();
});

function registerPassingDatabaseSizeCheck()
{
    Health::checks([
        FakeDatabaseSizeCheck::new()
            ->fakeDatabaseSizeInGb(0.5),
    ]);
}
