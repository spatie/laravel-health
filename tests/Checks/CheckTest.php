<?php

use Carbon\Carbon;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

use function Spatie\PestPluginTestTime\testTime;

it('uses UTC as a default timezone', function () {
    $check = new class extends Check {
        public function run(): Result
        {
            return Result::make();
        }
    };

    $check->dailyAt('00:00');

    testTime()->freeze(Carbon::make('2021-01-01 00:00:00'));

    expect($check->shouldRun())->toBeTrue();
});

it('takes a timezone into account', function () {
    $check = new class extends Check {
        protected string|DateTimeZone $timezone = 'America/Los_Angeles';

        public function run(): Result
        {
            return Result::make();
        }
    };

    $check->dailyAt('00:00');

    testTime()->freeze(Carbon::make('2021-01-01 00:00:00'));

    // Should not run because it is 00:00 UTC
    expect($check->shouldRun())->toBeFalse();

    testTime()->freeze(Carbon::make('2021-01-01 00:00:00', 'America/Los_Angeles')->utc());

    // Should run, because it's 00:00 America/Los_Angeles time
    expect($check->shouldRun())->toBeTrue();
});
