<?php

namespace Spatie\Health\Tests\Checks;

use Exception;
use Spatie\Health\Checks\Checks\RedisCheck;
use Spatie\Health\Enums\Status;
use Spatie\Health\Tests\TestClasses\FakeRedisCheck;

it('will return ok when redis is running', function () {
    $result = RedisCheck::new()->run();

    expect($result->status)->toBe(Status::ok());
})->skip(fn () => extension_loaded('redis') !== true, 'The redis extension is not loaded.');

it('will return an error when it cannot connect to Redis', function () {
    $result = FakeRedisCheck::new()
        ->replyWith(fn () => false)
        ->run();

    expect($result)
        ->status->toBe(Status::failed())
        ->notificationMessage->toBe('Redis returned a falsy response when try to connection to it.');
});

it('will return an error when connecting to redis throws an exception', function () {
    $result = FakeRedisCheck::new()
        ->replyWith(fn () => throw new Exception('This is an exception'))
        ->run();

    expect($result)
        ->status->toBe(Status::failed())
        ->notificationMessage->toBe('An exception occurred when connecting to Redis: `This is an exception`');
});
