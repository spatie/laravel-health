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
