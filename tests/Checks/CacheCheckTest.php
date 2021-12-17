<?php

use Spatie\Health\Checks\Checks\CacheCheck;
use Spatie\Health\Enums\Status;

it('will determine that a working cache is ok', function () {
    $result = CacheCheck::new()->run();

    expect($result->status)->toBe(Status::ok());
});


it('will determine that a non-existing cache is not ok', function () {
    $result = CacheCheck::new()
        ->driver('does-not-exist')
        ->run();

    expect($result)
        ->status->toBe(Status::failed())
        ->getNotificationMessage()->toBe('An exception occured with the application cache: `Cache store [does-not-exist] is not defined.`');
});
