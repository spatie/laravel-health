<?php

use Spatie\Health\Facades\Health;
use Spatie\Health\Tests\TestClasses\FakeUsedDiskSpaceCheck;
use function Pest\Laravel\artisan;
use Spatie\Health\Commands\ListChecksCommand;

it('thrown no exceptions with no check registered', function () {
    artisan(ListChecksCommand::class)->assertSuccessful();
});

it('thrown no exceptions with a check registered', function () {
    Health::checks([
        FakeUsedDiskSpaceCheck::class,
    ]);

    artisan(ListChecksCommand::class)->assertSuccessful();
});
