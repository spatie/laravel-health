<?php

use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\PingCheck;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;
use Spatie\Health\Checks\Result;
use Spatie\Health\Enums\Status;
use Spatie\Health\Exceptions\DuplicateCheckNamesFound;
use Spatie\Health\Exceptions\InvalidCheck;
use Spatie\Health\Facades\Health;
use Spatie\Health\Testing\FakeCheck;

it('can register checks', function () {
    Health::checks([
        UsedDiskSpaceCheck::new(),
    ]);

    expect(Health::registeredChecks())
        ->toHaveCount(1)
        ->and(Health::registeredChecks()[0])
        ->toBeInstanceOf(UsedDiskSpaceCheck::class);
});

it('will throw an exception when duplicate checks are registered', function () {
    Health::checks([
        PingCheck::new(),
        PingCheck::new(),
    ]);
})->throws(DuplicateCheckNamesFound::class);

it('will not throw an exception when all checks have unique names', function () {
    Health::checks([
        PingCheck::new(),
        PingCheck::new()->name('OtherPingCheck'),
    ]);

    expect(Health::registeredChecks())->toHaveCount(2);
});

it('will throw an exception when registering a class that does not exist Check', function () {
    Health::checks([
        new StdClass(),
    ]);
})->throws(InvalidCheck::class);

it('will throw an exception when registering a string', function () {
    Health::checks([
        PingCheck::class,
    ]);
})->throws(InvalidCheck::class);

it('can fake checks', function () {
    Health::checks([
        DatabaseCheck::new(),
        PingCheck::new(),
    ]);

    Health::fake([
        DatabaseCheck::class => new Result(
            Status::crashed(),
            "We're just making sure faking works",
            "Hey, faking works!",
        ),
        PingCheck::class => FakeCheck::result(
            new Result(Status::warning()),
            true
        ),
    ]);

    $this->artisan('health:check')
        ->expectsOutputToContain(ucfirst((string) Status::crashed()->value))
        ->expectsOutputToContain(ucfirst((string) Status::warning()->value));
});

it('can pass a closure to fake checks', function () {
    Health::checks([
        DatabaseCheck::new()->name('MySQL')->connectionName('mysql'),
        DatabaseCheck::new()->name('DB SQLite')->connectionName('sqlite'),
    ]);

    Health::fake([
        DatabaseCheck::class => function (DatabaseCheck $check) {
            return $check->getName() === 'MySQL'
                ? new Result(Status::crashed())
                : new Result(Status::warning());
        }
    ]);

    $this->artisan('health:check')
        ->expectsOutputToContain(ucfirst((string) Status::crashed()->value))
        ->expectsOutputToContain(ucfirst((string) Status::warning()->value));
});
