<?php

use Carbon\Carbon;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

use function Spatie\PestPluginTestTime\testTime;

it('uses UTC as a default timezone', function () {
    $check = new class extends Check
    {
        public function run(): Result
        {
            return Result::make();
        }
    };

    $check->dailyAt('00:00');

    testTime()->freeze(Carbon::make('2021-01-01 00:00:00'));

    expect($check->shouldRun())->toBeTrue();
});

it('does not run at the incorrect time', function (string $incorrectTime) {
    $check = new class extends Check
    {
        public function run(): Result
        {
            return Result::make();
        }
    };

    $check->dailyAt('12:00');

    [$hour, $minutes] = explode(':', $incorrectTime);
    testTime()->freeze(Carbon::make('2021-01-01 08:00:00')->setTime($hour, $minutes));

    expect($check->shouldRun())->toBeFalse();
})->with([
    '00:00', '11:00', '11:59', '12:01',
]);

it('takes a timezone into account', function (Check $check) {
    $check->dailyAt('00:00');

    testTime()->freeze(Carbon::make('2021-01-01 00:00:00'));

    // Should not run because it is 00:00 UTC
    expect($check->shouldRun())->toBeFalse();

    testTime()->freeze(Carbon::make('2021-01-01 08:00:00'));

    // Should run, because it's 00:00 America/Los_Angeles time / 08:00 UTC
    expect($check->shouldRun())->toBeTrue();
})->with([
    'Explicit timezone as property' => new class extends Check
    {
        protected string|DateTimeZone $timezone = 'America/Los_Angeles';

        public function run(): Result
        {
            return Result::make();
        }
    },
    'Timezone method call' => (new class extends Check
    {
        public function run(): Result
        {
            return Result::make();
        }
    })->timezone('America/Los_Angeles'),
]);
