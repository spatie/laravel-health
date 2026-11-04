<?php

namespace Spatie\Health\Tests\Checks;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Spatie\Health\Checks\Checks\HorizonCheck;
use Spatie\Health\Enums\Status;

it('will fail when horizon is not running', function () {
    $this->fakeHorizonStatus('down');

    $result = HorizonCheck::new()->run();

    expect($result)
        ->status->toBe(Status::failed())
        ->notificationMessage->toBe('Horizon is not running.');
});

it('will send a warning when horizon is paused', function () {
    $this->fakeHorizonStatus('paused');

    $result = HorizonCheck::new()->run();

    expect($result)
        ->status->toBe(Status::warning())
        ->notificationMessage->toBe('Horizon is running, but the status is paused.');
});

it('will determine that a running horizon is ok', function () {
    $this->fakeHorizonStatus('running');

    $result = HorizonCheck::new()->run();

    expect($result)->status->toBe(Status::ok());
});

it('pings heartbeat url when explicitly set', function () {
    Http::fake();

    $this->fakeHorizonStatus('running');

    HorizonCheck::new()
        ->heartbeatUrl('https://example.com/explicit-heartbeat')
        ->run();

    Http::assertSent(function ($request) {
        return $request->url() === 'https://example.com/explicit-heartbeat';
    });
});

it('falls back to config heartbeat url when no url is explicitly set', function () {
    Http::fake();
    config()->set('health.horizon.heartbeat_url', 'https://example.com/config-heartbeat');

    $this->fakeHorizonStatus('running');

    HorizonCheck::new()
        ->run();

    Http::assertSent(function ($request) {
        return $request->url() === 'https://example.com/config-heartbeat';
    });
});

it('does not ping when heartbeat url is not set and no config fallback exists', function () {
    Http::fake();
    config()->set('health.horizon.heartbeat_url', null);

    $this->fakeHorizonStatus('running');

    HorizonCheck::new()->run();

    Http::assertNothingSent();
});

it('will return warning on first failure when failAfterMinutes is set', function () {
    $this->fakeHorizonStatus('down');

    $result = HorizonCheck::new()
        ->failAfterMinutes(30)
        ->run();

    expect($result->status)->toBe(Status::warning());
});

it('will return failed after grace period expires', function () {
    $check = HorizonCheck::new()
        ->failAfterMinutes(30);

    $this->fakeHorizonStatus('down');

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
    $check = HorizonCheck::new()
        ->failAfterMinutes(30);

    $this->fakeHorizonStatusSequence([
        'down',     // first run → warning
        'running',  // second run → ok (clears cache)
        'down',     // third run → warning again
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
    $check = HorizonCheck::new()
        ->failAfterMinutes(30);

    $this->fakeHorizonStatus('down');

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
