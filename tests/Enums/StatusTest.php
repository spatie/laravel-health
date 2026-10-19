<?php

use Spatie\Health\Checks\Result;
use Spatie\Health\Enums\Status;

it('preserves status values, summaries and Slack colors', function (Status $status, string $value, string $color) {
    expect($status->value)->toBe($value)
        ->and(Status::from($value))->toBe($status)
        ->and(json_encode($status))->toBe('"'.$value.'"')
        ->and((new Result($status))->getShortSummary())->toBe(ucfirst($value))
        ->and($status->getSlackColor())->toBe($color);
})->with([
    [Status::Ok, 'ok', '#2EB67D'],
    [Status::Warning, 'warning', '#ECB22E'],
    [Status::Failed, 'failed', '#E01E5A'],
    [Status::Crashed, 'crashed', '#E01E5A'],
    [Status::Skipped, 'skipped', ''],
]);
