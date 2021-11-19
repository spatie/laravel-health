<?php

use Spatie\Health\Checks\DiskSpaceCheck;
use Spatie\Health\Enums\Status;
use Spatie\Health\Support\Result;
use Spatie\Health\Tests\TestClasses\FakeDiskSpaceCheck;

it('will return ok if the used disk space does not cross the threshold', function() {
    $result = FakeDiskSpaceCheck::new()
        ->fakeDiskUsagePercentage(10)
        ->warnWhenFreeSpaceIsAbovePercentage(70)
        ->errorWhenFreeSpaceIsAbovePercentage(90)
        ->run();

    expect($result)
        ->toBeInstanceOf(Result::class)
        ->status->toBe(Status::ok())
        ->meta->toEqual(['disk_space_used_percentage' => 10]);
});

it('will return a warning if the used disk space does cross the warning threshold', function() {
    $result = FakeDiskSpaceCheck::new()
        ->fakeDiskUsagePercentage(71)
        ->warnWhenFreeSpaceIsAbovePercentage(70)
        ->errorWhenFreeSpaceIsAbovePercentage(90)
        ->run();

    expect($result)
        ->toBeInstanceOf(Result::class)
        ->status->toEqual(Status::warning())
        ->meta->toEqual(['disk_space_used_percentage' => 71])
        ->getMessage()->toEqual('The disk is almost full: (71 % used)');
});

it('will return an error if the used disk space does cross the error threshold', function() {
    $result = FakeDiskSpaceCheck::new()
        ->fakeDiskUsagePercentage(91)
        ->warnWhenFreeSpaceIsAbovePercentage(70)
        ->errorWhenFreeSpaceIsAbovePercentage(90)
        ->run();

    expect($result)
        ->toBeInstanceOf(Result::class)
        ->status->toEqual(Status::failed())
        ->meta->toEqual(['disk_space_used_percentage' => 91])
        ->getMessage()->toEqual('The disk is almost full: (91 % used)');
});

it('can report the real disk space used', function() {
    $result = DiskSpaceCheck::new()->run();

    expect($result->meta['disk_space_used_percentage'])->between(0, 100);
});
