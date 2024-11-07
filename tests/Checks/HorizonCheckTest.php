<?php

namespace Spatie\Health\Tests\Checks;

use Spatie\Health\Checks\Checks\HorizonCheck;
use Spatie\Health\Enums\Status;
use Illuminate\Support\Facades\Http;

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

it('pings heartbeat url when configured', function () {
    Http::fake();
    config()->set('health.horizon.heartbeat_url', 'https://example.com/heartbeat');

    $this->fakeHorizonStatus('running');

    HorizonCheck::new()->run();

    Http::assertSent(function ($request) {
        return $request->url() === 'https://example.com/heartbeat';
    });
});

it('does not ping heartbeat url when not configured', function () {
    Http::fake();
    config()->set('health.horizon.heartbeat_url', null);

    $this->fakeHorizonStatus('running');

    HorizonCheck::new()->run();
    Http::assertNothingSent();
});
