<?php

use Spatie\Health\Checks\Checks\EnvironmentCheck;
use Spatie\Health\Enums\Status;

it('will determine that a correct environment is ok', function () {
    $result = EnvironmentCheck::new()
        ->expectEnvironment('testing')
        ->run();

    expect($result->status)->toBe(Status::ok());
});

it('will determine that a wrong environment is not ok', function () {
    $result = EnvironmentCheck::new()
        ->expectEnvironment('production')
        ->run();

    expect($result)
        ->status->toBe(Status::failed())
        ->getNotificationMessage()->toBe('The environment was expected to be `production`, but actually was `testing`');
});
