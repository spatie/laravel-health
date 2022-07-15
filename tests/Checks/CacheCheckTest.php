<?php

use Spatie\Health\Checks\Checks\CacheCheck;
use Spatie\Health\Enums\Status;
use Spatie\Health\Tests\TestClasses\FakeCacheCheck;

it('will determine that a working cache is ok', function () {
    $result = CacheCheck::new()->run();

    expect($result->status)->toBe(Status::ok());
});

it('will determine that a non-existing cache is not ok', function () {
    $result = CacheCheck::new()
        ->driver('does-not-exist')
        ->run();

    expect($result)
        ->status->toBe(Status::failed())
        ->getNotificationMessage()->toBe('An exception occurred with the application cache: `Cache store [does-not-exist] is not defined.`');
});

it('will return an error when it cannot set or retrieve cache key', function () {
    $result = FakeCacheCheck::new()
        ->replyWith(fn () => false)
        ->run();

    expect($result)
        ->status->toBe(Status::failed())
        ->notificationMessage->toBe('Could not set or retrieve an application cache value.');
});
