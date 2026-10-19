<?php

namespace Spatie\Health\Tests\Checks;

use ArgumentCountError;
use Exception;
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

it('can check a named redis cluster connection', function () {
    config()->set('database.redis.client', 'phpredis');
    config()->set('database.redis.clusters.health-cluster', [
        ['host' => '127.0.0.1', 'port' => getenv('REDIS_CLUSTER_PORT')],
    ]);

    $result = RedisCheck::new()->connectionName('health-cluster')->run();

    expect($result)
        ->status->toBe(Status::ok())
        ->meta->toBe(['connection_name' => 'health-cluster']);
})->skip(fn () => ! getenv('REDIS_CLUSTER_PORT'), 'A Redis cluster is not configured.');

it('preserves non-cluster ping responses', function (bool|string $response, Status $status) {
    $connection = Mockery::mock(PhpRedisConnection::class);
    $connection->shouldReceive('ping')
        ->once()
        ->withNoArgs()
        ->andReturn($response);

    Redis::shouldReceive('connection')
        ->with('default')
        ->andReturn($connection);

    expect(RedisCheck::new()->run()->status)->toBe($status);
})->with([
    'boolean pong' => [true, Status::ok()],
    'string pong' => ['PONG', Status::ok()],
    'legacy pong' => ['+PONG', Status::ok()],
    'failed ping' => [false, Status::failed()],
]);

it('fails gracefully when pinging throws an error instead of aborting the run', function () {
    $result = FakeRedisCheck::new()
        ->replyWith(fn () => throw new ArgumentCountError('RedisCluster::ping() expects at least 1 argument, 0 given'))
        ->run();

    expect($result)
        ->status->toBe(Status::failed())
        ->notificationMessage->toBe('An exception occurred when connecting to Redis: `RedisCluster::ping() expects at least 1 argument, 0 given`');
});
