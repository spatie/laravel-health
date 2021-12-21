<?php

use Illuminate\Support\Facades\Http;
use Spatie\Health\Checks\Checks\PingCheck;
use Spatie\Health\Enums\Status;

it('will determine that a working site is ok', function () {
    Http::fake([
        '*' => Http::response(status: 200),
    ]);

    $result = PingCheck::new()
        ->url('https://dummy-url.com')
        ->run();

    expect($result->status)->toBe(Status::ok());
});

it('will determine that a non-existing site is not ok', function () {
    Http::fake([
        '*' => Http::response(status: 500),
    ]);

    $result = PingCheck::new()
        ->url('https://dummy-url.com')
        ->run();

    expect($result->status)->toBe(Status::failed());
});

it('when the http client throws an exception, then the check will fail', function () {
    $result = PingCheck::new()
        ->url('https://test.com')
        ->run();

    expect($result->status)->toBe(Status::failed());
});
