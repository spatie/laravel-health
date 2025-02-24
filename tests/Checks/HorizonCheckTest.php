<?php

namespace Spatie\Health\Tests\Checks;

use Illuminate\Support\Facades\Http;
use Spatie\Health\Checks\Checks\HorizonCheck;
use Spatie\Health\Enums\Status;

it('will fail when horizon is not running', function () {
    $result = HorizonCheck::new()->run();

    expect($result)
        ->status->toBe(Status::failed())
        ->notificationMessage->toBe('Horizon is not running.');
})->skip(fn () => extension_loaded('redis') !== true, 'The redis extension is not loaded.');

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
