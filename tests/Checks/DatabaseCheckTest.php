<?php

use Spatie\Health\Checks\DatabaseCheck;
use Spatie\Health\Enums\Status;

it('will determine if that wa working database connection is ok', function () {
    $result = DatabaseCheck::new()
        ->connectionName('testing')
        ->run();

    expect($result->status)->toBe(Status::ok());
});

it('will determine if that a non-existing database connection is not ok', function () {
    $result = DatabaseCheck::new()
        ->connectionName('does-not-exist')
        ->run();

    expect($result->status)->toBe(Status::failed());
});
