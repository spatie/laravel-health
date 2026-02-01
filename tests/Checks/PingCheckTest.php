<?php

use Carbon\Carbon;
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

it('will return warning on first failure when failAfterMinutes is set', function () {
    Http::fake([
        '*' => Http::response(status: 500),
    ]);

    $result = PingCheck::new()
        ->name('test-ping')
        ->url('https://dummy-url.com')
        ->failAfterMinutes(30)
        ->run();

    expect($result->status)->toBe(Status::warning());
});

it('will return failed after grace period expires', function () {
    Http::fake([
        '*' => Http::response(status: 500),
    ]);

    $check = PingCheck::new()
        ->name('test-ping-grace')
        ->url('https://dummy-url.com')
        ->failAfterMinutes(30);

    // First failure - should be warning
    $result = $check->run();
    expect($result->status)->toBe(Status::warning());

    // Travel 31 minutes into the future
    Carbon::setTestNow(now()->addMinutes(31));

    // Second failure after grace period - should be failed
    $result = $check->run();
    expect($result->status)->toBe(Status::failed());

    Carbon::setTestNow();
});

it('will reset failure cache on successful request', function () {
    $check = PingCheck::new()
        ->name('test-ping-reset')
        ->url('https://dummy-url.com')
        ->failAfterMinutes(30);

    // Use sequence to simulate: failure -> success -> failure
    Http::fake([
        '*' => Http::sequence()
            ->push(status: 500)  // First: failure
            ->push(status: 200)  // Second: success
            ->push(status: 500), // Third: failure
    ]);

    // First: failure - should be warning
    $result = $check->run();
    expect($result->status)->toBe(Status::warning());

    // Second: success - should clear cache and return ok
    $result = $check->run();
    expect($result->status)->toBe(Status::ok());

    // Third: failure again - should be warning (cache was cleared)
    $result = $check->run();
    expect($result->status)->toBe(Status::warning());
});

it('will return warning during grace period', function () {
    Http::fake([
        '*' => Http::response(status: 500),
    ]);

    $check = PingCheck::new()
        ->name('test-ping-grace-period')
        ->url('https://dummy-url.com')
        ->failAfterMinutes(30);

    // First failure
    $result = $check->run();
    expect($result->status)->toBe(Status::warning());

    // Travel 15 minutes (still within grace period)
    Carbon::setTestNow(now()->addMinutes(15));

    // Second failure - still warning
    $result = $check->run();
    expect($result->status)->toBe(Status::warning());

    // Reset time
    Carbon::setTestNow();
});
