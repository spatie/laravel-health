<?php

use Spatie\Health\Checks\DiskSpaceCheck;
use Spatie\Health\Facades\Health;

it('can register checks', function () {
    Health::checks([
        DiskSpaceCheck::new(),
    ]);

    expect(Health::registeredChecks())
        ->toHaveCount(1)
        ->and(Health::registeredChecks()[0])
        ->toBeInstanceOf(DiskSpaceCheck::class);
});
