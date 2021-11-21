<?php

use Spatie\Health\Checks\DiskSpaceCheck2;
use Spatie\Health\Facades\Health;

it('can register checks', function () {
    Health::checks([
        DiskSpaceCheck2::new(),
    ]);

    expect(Health::registeredChecks())
        ->toHaveCount(1)
        ->and(Health::registeredChecks()[0])
        ->toBeInstanceOf(DiskSpaceCheck2::class);
});
