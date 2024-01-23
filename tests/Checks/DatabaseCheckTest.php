<?php

use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Enums\Status;

it('will determine that a working database connection is ok', function () {
    $result = DatabaseCheck::new()
        ->connectionName('testing')
        ->run();

    expect($result->status)->toBe(Status::ok());
});

it('will determine that a non-existing database connection is not ok', function () {
    $result = DatabaseCheck::new()
        ->connectionName('does-not-exist')
        ->run();

    expect($result)
        ->status->toBe(Status::failed())
        ->getNotificationMessage()->toBe('Could not connect to the database: `Database connection [does-not-exist] not configured.`');
});
