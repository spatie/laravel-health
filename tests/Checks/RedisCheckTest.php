<?php

namespace Spatie\Health\Tests\Checks;

use Exception;
use Illuminate\Redis\Connections\PhpRedisClusterConnection;
use Illuminate\Redis\Connections\PhpRedisConnection;
use Illuminate\Support\Facades\Redis;
use Mockery;
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

it('routes the ping by a key on a redis cluster connection', function () {
    $connection = Mockery::mock(PhpRedisClusterConnection::class);
    $connection->shouldReceive('command')
        ->once()
        ->with('ping', ['laravel-health:redis:ping'])
        ->andReturnTrue();

    Redis::shouldReceive('connection')
        ->with('default')
        ->andReturn($connection);

    expect(RedisCheck::new()->run()->status)->toBe(Status::ok());
});

it('pings without a key on a non-cluster connection', function () {
    $connection = Mockery::mock(PhpRedisConnection::class);
    $connection->shouldReceive('ping')
        ->once()
        ->withNoArgs()
        ->andReturnTrue();

    Redis::shouldReceive('connection')
        ->with('default')
        ->andReturn($connection);

    expect(RedisCheck::new()->run()->status)->toBe(Status::ok());
});

it('fails gracefully when pinging throws an error instead of aborting the run', function () {
    $result = FakeRedisCheck::new()
        ->replyWith(fn () => throw new \Error('RedisCluster::ping() expects at least 1 argument, 0 given'))
        ->run();

    expect($result)
        ->status->toBe(Status::failed())
        ->notificationMessage->toBe('An exception occurred when connecting to Redis: `RedisCluster::ping() expects at least 1 argument, 0 given`');
});
