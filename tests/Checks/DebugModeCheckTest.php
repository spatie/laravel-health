<?php

use Spatie\Health\Checks\Checks\DebugModeCheck;
use Spatie\Health\Enums\Status;

it('will determine that a correct debug mode is ok', function () {
    $result = DebugModeCheck::new()
        ->run();
    expect($result->status)->toBe(Status::ok());

    $result = DebugModeCheck::new()
        ->expectedToBe(true)
        ->run();

    expect($result)
        ->status->toBe(Status::failed())
        ->getNotificationMessage()->toBe('The debug mode was expected to be `true`, but actually was `false`');
});
