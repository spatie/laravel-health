<?php

use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\Enums\Status;
use Spatie\Health\Facades\Health;
use Spatie\Health\Tests\TestClasses\InMemoryResultStore;

use function Pest\Laravel\artisan;
use function Spatie\PestPluginTestTime\testTime;

beforeEach(function () {
    config()->set('health.result_stores', InMemoryResultStore::class);

    testTime()->freeze('2021-01-01 00:00:00');

    $this->check = DatabaseCheck::new()
        ->everyFiveMinutes()
        ->connectionName('testing');

    Health::checks([
        $this->check,
    ]);
});

it('will return a skipped result for checks that should not run', function () {
    artisan(RunHealthChecksCommand::class);
    expect(InMemoryResultStore::$checkResults)->toHaveCount(1);
    expect(InMemoryResultStore::$checkResults[0]->status)->toBe(Status::ok());

    testTime()->addMinutes(4);
    artisan(RunHealthChecksCommand::class);
    expect(InMemoryResultStore::$checkResults)->toHaveCount(1);
    expect(InMemoryResultStore::$checkResults[0]->status)->toBe(Status::skipped());

    testTime()->addMinute();
    artisan(RunHealthChecksCommand::class);
    expect(InMemoryResultStore::$checkResults)->toHaveCount(1);
    expect(InMemoryResultStore::$checkResults[0]->status)->toBe(Status::ok());
});
