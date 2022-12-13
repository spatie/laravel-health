<?php

use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;
use Spatie\Health\Checks\Result;
use Spatie\Health\Enums\Status;
use Spatie\Health\Tests\TestClasses\FakeUsedDiskSpaceCheck;

it('will return ok if the used disk space does not cross the threshold', function () {
    $result = FakeUsedDiskSpaceCheck::new()
        ->fakeDiskUsagePercentage(10)
        ->warnWhenUsedSpaceIsAbovePercentage(70)
        ->failWhenUsedSpaceIsAbovePercentage(90)
        ->run();

    expect($result)
        ->toBeInstanceOf(Result::class)
        ->status->toBe(Status::ok())
        ->meta->toEqual(['disk_space_used_percentage' => 10]);
});

it('will return a warning if the used disk space does cross the warning threshold', function () {
    $result = FakeUsedDiskSpaceCheck::new()
        ->fakeDiskUsagePercentage(71)
        ->warnWhenUsedSpaceIsAbovePercentage(70)
        ->failWhenUsedSpaceIsAbovePercentage(90)
        ->run();

    expect($result)
        ->toBeInstanceOf(Result::class)
        ->status->toEqual(Status::warning())
        ->meta->toEqual(['disk_space_used_percentage' => 71])
        ->getNotificationMessage()->toEqual('The disk is almost full (71% used).');
});

it('will return an error if the used disk space does cross the error threshold', function () {
    $result = FakeUsedDiskSpaceCheck::new()
        ->fakeDiskUsagePercentage(91)
        ->warnWhenUsedSpaceIsAbovePercentage(70)
        ->failWhenUsedSpaceIsAbovePercentage(90)
        ->run();

    expect($result)
        ->toBeInstanceOf(Result::class)
        ->status->toEqual(Status::failed())
        ->meta->toEqual(['disk_space_used_percentage' => 91])
        ->getNotificationMessage()->toEqual('The disk is almost full (91% used).');
});

it('can set a custom filesystem name', function () {
    $filesystem = '/mnt/temp';

    $check = FakeUsedDiskSpaceCheck::new()
        ->filesystemName('/mnt/temp');

    expect($check->getFilesystemName())
        ->toEqual($filesystem);
});

it('can report the real disk space used', function () {
    $result = UsedDiskSpaceCheck::new()->run();

    expect($result->meta['disk_space_used_percentage'])->between(0, 100);
});
