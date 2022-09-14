<?php

use function Pest\Laravel\artisan;
use Illuminate\Support\Facades\App;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\Enums\Status;
use Spatie\Health\Facades\Health;
use Spatie\Health\Tests\TestClasses\InMemoryResultStore;

beforeEach(function () {
    config()->set('health.result_stores', InMemoryResultStore::class);

    $this->check = DatabaseCheck::new()
        ->connectionName('testing');

    Health::checks([
        $this->check,
    ]);
});

it('will run checks only on the actual environment', function () {
    $actualEnvironment = App::environment();

    $this
        ->check
        ->environments($actualEnvironment);

    artisan(RunHealthChecksCommand::class);

    expect(InMemoryResultStore::$checkResults)->toHaveCount(1);
    expect(InMemoryResultStore::$checkResults[0]->status)->toBe(Status::ok());
});

it('will run checks if the actual environment exists in a provided array', function () {
    $actualEnvironment = App::environment();

    $this
        ->check
        ->environments([$actualEnvironment, 'other-environment']);

    artisan(RunHealthChecksCommand::class);

    expect(InMemoryResultStore::$checkResults)->toHaveCount(1);
    expect(InMemoryResultStore::$checkResults[0]->status)->toBe(Status::ok());
});


it('will skip checks for other environments', function () {
    $this
        ->check
        ->environments('other-environment');

    artisan(RunHealthChecksCommand::class);

    expect(InMemoryResultStore::$checkResults)->toHaveCount(1);
    expect(InMemoryResultStore::$checkResults[0]->status)->toBe(Status::skipped());
});
