<?php

namespace Spatie\Health\Tests\Checks;

use Spatie\Health\Checks\Result;
use Spatie\Health\Enums\Status;
use Spatie\Health\Tests\TestClasses\FakeRedisMemoryUsageCheck;

it('will return ok if the memory usage does not cross the threshold', function () {
    $result = FakeRedisMemoryUsageCheck::new()
        ->fakeMemoryUsageInMb(999)
        ->failWhenAboveMb(1000)
        ->run();

    expect($result)
        ->toBeInstanceOf(Result::class)
        ->status->toBe(Status::ok());
});

it('will return an error if the used memory does cross the threshold', function () {
    $result = FakeRedisMemoryUsageCheck::new()
        ->fakeMemoryUsageInMb(1001)
        ->failWhenAboveMb(1000)
        ->run();

    expect($result)
        ->toBeInstanceOf(Result::class)
        ->status->toEqual(Status::failed())
        ->getNotificationMessage()->toEqual('Redis memory usage is 1001 MB, which is above the threshold of 1000 MB.');
});
