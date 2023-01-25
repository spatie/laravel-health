<?php

use Spatie\Health\Checks\Checks\DatabaseSizeCheck;
use Spatie\Health\Enums\Status;

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
