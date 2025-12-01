<?php

use Carbon\Carbon;
use Composer\InstalledVersions;
use Illuminate\Support\Facades\Mail;
use Spatie\Health\Facades\Health;
use Spatie\Health\Tests\TestCase;
use Spatie\Health\Tests\TestClasses\FakeUsedDiskSpaceCheck;

uses(TestCase::class)
    ->beforeEach(function () {
        Mail::fake();
    })
    ->in(__DIR__);

expect()->extend('between', function (int $min, $max) {
    expect($this->value)
        ->toBeGreaterThanOrEqual($min)
        ->toBeLessThanOrEqual($max);

    return $this;
});

function getTemporaryDirectory(string $path = ''): string
{
    return __DIR__."/temp/{$path}";
}

function addTestFile(string $path, ?Carbon $date = null, ?int $sizeInMb = null): void
{
    $date = $date ?? now();

    file_put_contents($path, 'content');

    touch($path, $date->timestamp);

    if ($sizeInMb) {
        shell_exec("truncate -s {$sizeInMb}M {$path}");
    }
}

function skipOnOldCarbon(): void
{
    $carbonVersion = InstalledVersions::getVersion('nesbot/carbon');

    if (version_compare($carbonVersion, '3.0.0', '<')) {
        test()->markTestSkipped();
    }
}

function registerPassingCheck(): void
{
    Health::checks([
        FakeUsedDiskSpaceCheck::new()
            ->failWhenUsedSpaceIsAbovePercentage(10)
            ->fakeDiskUsagePercentage(0),
    ]);
}

function registerFailingCheck(): void
{
    Health::checks([
        FakeUsedDiskSpaceCheck::new()
            ->failWhenUsedSpaceIsAbovePercentage(10)
            ->fakeDiskUsagePercentage(11),
    ]);
}
