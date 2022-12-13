<?php

use Spatie\Health\Checks\Checks\DatabaseConnectionCountCheck;
use Spatie\Health\Enums\Status;

it('will determine that connection count is ok if it does not cross the maximum', function () {
    $result = DatabaseConnectionCountCheck::new()
        ->connectionName('mysql')
        ->failWhenMoreConnectionsThan(50)
        ->run();

    expect($result->status)->toBe(Status::ok());
    expect($result->meta['connection_count'])->toBeGreaterThan(0);
});

it('will determine that connection count is not ok if it does cross the maximum', function () {
    $result = DatabaseConnectionCountCheck::new()
        ->failWhenMoreConnectionsThan(0)
        ->run();

    expect($result->status)->toBe(Status::failed());
    expect($result->getNotificationMessage())->toStartWith('There are too many database connections');
});

it('will determine that connection count is not ok if it does cross the warning threshold', function () {
    $result = DatabaseConnectionCountCheck::new()
        ->warnWhenMoreConnectionsThan(0)
        ->run();

    expect($result->status)->toBe(Status::warning());

    expect($result->getNotificationMessage())->toContain('connection');
});
