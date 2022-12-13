<?php

use Spatie\Health\Checks\Checks\DatabaseTableSizeCheck;
use Spatie\Health\Enums\Status;

it('will determine that table size is ok if it does not cross the maximum', function () {
    $result = DatabaseTableSizeCheck::new()
        ->connectionName('mysql')
        ->table('health_check_result_history_items', 50)
        ->run();

    expect($result->status)->toBe(Status::ok());
    expect($result->meta['health_check_result_history_items'])->toBeGreaterThan(0);
});

it('will determine that table size is not ok if it does cross the maximum', function () {
    $result = DatabaseTableSizeCheck::new()
        ->connectionName('mysql')
        ->table('health_check_result_history_items', 0)
        ->run();

    expect($result->status)->toBe(Status::failed());
    expect($result->getNotificationMessage())->toStartWith('This table is too big:');
});
