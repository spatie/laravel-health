<?php

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use Spatie\Health\Checks\Checks\HorizonCheck;
use Spatie\Health\Checks\Checks\PingCheck;
use Spatie\Health\Checks\Checks\QueueCheck;
use Spatie\Health\Enums\Status;

beforeEach(function () {
    config()->set('cache.stores.serialized-array', ['driver' => 'array', 'serialize' => true]);
    config()->set('cache.stores.failure-file', [
        'driver' => 'file',
        'path' => getTemporaryDirectory('failure-cache'),
    ]);

    $this->fakeHorizonStatus('down');
});

afterEach(function () {
    cache()->store('failure-file')->flush();
    Carbon::setTestNow();
    CarbonImmutable::setTestNow();
    Date::useDefault();
});

it('preserves the exact grace period with serialized caches and resets after recovery', function (string $checkClass, string $store, string $dateClass) {
    config()->set('cache.serializable_classes', false);
    Http::fakeSequence()->pushStatus(500)->pushStatus(500)->pushStatus(500)->pushStatus(200)->pushStatus(500);
    Date::use($dateClass);
    $startedAt = CarbonImmutable::parse('2026-03-29T01:50:00.750000+01:00');
    Carbon::setTestNow($startedAt);
    CarbonImmutable::setTestNow($startedAt);

    $check = $checkClass::new()->useCacheStore($store)->failAfterMinutes(30);

    if ($check instanceof PingCheck) {
        $check->url('https://example.com');
    }

    expect($check->run()->status)->toBe(Status::warning());

    Carbon::setTestNow('2026-03-29T01:20:00.749999+00:00');
    CarbonImmutable::setTestNow('2026-03-29T01:20:00.749999+00:00');
    expect($check->run()->status)->toBe(Status::warning());

    Carbon::setTestNow('2026-03-29T03:20:00.750000+02:00');
    CarbonImmutable::setTestNow('2026-03-29T03:20:00.750000+02:00');
    expect($check->run()->status)->toBe(Status::failed());

    $this->fakeHorizonStatus('running');

    if ($check instanceof QueueCheck) {
        cache()->store($store)->put($check->getHeartbeatCacheKey('default'), now()->timestamp);
    }

    expect($check->run()->status)->toBe(Status::ok());

    $this->fakeHorizonStatus('down');

    if ($check instanceof QueueCheck) {
        cache()->store($store)->forget($check->getHeartbeatCacheKey('default'));
    }

    expect($check->run()->status)->toBe(Status::warning());
})->with([HorizonCheck::class, PingCheck::class, QueueCheck::class])
    ->with(['serialized-array', 'failure-file'])
    ->with([[Carbon::class], [CarbonImmutable::class]]);

it('keeps the grace period of readable cached dates and strings', function (string $store, string $format) {
    config()->set('cache.serializable_classes', true);
    $startedAt = CarbonImmutable::parse('2026-01-01T12:00:00.750000+05:45');
    $value = match ($format) {
        'mutable' => $startedAt->toMutable(),
        'immutable' => $startedAt,
        'string' => '2026-01-01T12:00:00.750000+05:45',
    };
    $check = HorizonCheck::new()->useCacheStore($store)->failAfterMinutes(30);
    cache()->store($store)->put($check->getCacheKey(), $value);

    Carbon::setTestNow('2026-01-01T06:44:59.750000+00:00');
    expect($check->run()->status)->toBe(Status::warning());

    Carbon::setTestNow('2026-01-01T06:45:00.750000+00:00');
    expect($check->run()->status)->toBe(Status::failed());
})->with(['serialized-array', 'failure-file'])->with(['mutable', 'immutable', 'string']);

it('resets an unreadable legacy date only once', function (string $store) {
    config()->set('cache.serializable_classes', false);
    Carbon::setTestNow('2026-01-01T12:00:00.750000+00:00');
    $check = HorizonCheck::new()->useCacheStore($store)->failAfterMinutes(30);
    cache()->store($store)->put($check->getCacheKey(), now()->subHour());

    $classesAreBlocked = cache()->store($store)->get($check->getCacheKey()) instanceof __PHP_Incomplete_Class;
    expect($check->run()->status)->toBe($classesAreBlocked ? Status::warning() : Status::failed());

    Carbon::setTestNow('2026-01-01T12:29:59.750000+00:00');
    expect($check->run()->status)->toBe($classesAreBlocked ? Status::warning() : Status::failed());

    Carbon::setTestNow('2026-01-01T12:30:00.750000+00:00');
    expect($check->run()->status)->toBe(Status::failed());

    Carbon::setTestNow('2026-01-01T12:31:00.750000+00:00');
    expect($check->run()->status)->toBe(Status::failed());
})->with(['serialized-array', 'failure-file']);
