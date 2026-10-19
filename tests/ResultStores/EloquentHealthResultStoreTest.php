<?php

use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\Database\Factories\HealthCheckResultHistoryItemFactory;
use Spatie\Health\Enums\Status;
use Spatie\Health\Facades\Health;
use Spatie\Health\Models\HealthCheckResultHistoryItem;
use Spatie\Health\ResultStores\EloquentHealthResultStore;
use Spatie\Health\ResultStores\ResultStore;
use Spatie\Health\Tests\TestClasses\FakeUsedDiskSpaceCheck;

use function Pest\Laravel\artisan;
use function Spatie\PestPluginTestTime\testTime;
use function Spatie\Snapshots\assertMatchesJsonSnapshot;

beforeEach(function () {
    testTime()->freeze('2021-01-01 00:00:00');

    config()->set('health.result_stores', [
        EloquentHealthResultStore::class,
    ]);

    $this->fakeDiskSpaceCheck = FakeUsedDiskSpaceCheck::new();

    Health::checks([
        $this->fakeDiskSpaceCheck,
    ]);
});

it('can write check results to the database', function () {
    artisan(RunHealthChecksCommand::class)->assertSuccessful();

    expect(HealthCheckResultHistoryItem::get())->toHaveCount(1);
});

it('can create history items directly through the package factory', function () {
    $item = HealthCheckResultHistoryItemFactory::new()->create([
        'status' => Status::Warning,
    ]);

    expect($item)->toBeInstanceOf(HealthCheckResultHistoryItem::class)
        ->and($item->fresh()->status)->toBe(Status::Warning)
        ->and($item->getRawOriginal('status'))->toBe('warning');
});

it('retains package casts when a custom history model defines additional casts', function () {
    $model = new class extends HealthCheckResultHistoryItem
    {
        protected $table = 'health_check_result_history_items';

        protected function casts(): array
        {
            return ['check_label' => 'string'];
        }
    };

    config()->set('health.result_stores', [
        EloquentHealthResultStore::class => ['model' => $model::class],
    ]);

    artisan(RunHealthChecksCommand::class)->assertSuccessful();

    $item = $model->newQuery()->sole();

    expect($item->status)->toBe(Status::Ok)
        ->and($item->meta)->toBeArray()
        ->and(app(ResultStore::class)->latestResults()->storedCheckResults->sole()->status)->toBe(Status::Ok);
});

it('can retrieve the latest results from json', function () {
    $report = app(ResultStore::class)->latestResults();
    expect($report)->toBeNull();

    artisan(RunHealthChecksCommand::class)->assertSuccessful();

    $report = app(ResultStore::class)->latestResults();

    assertMatchesJsonSnapshot($report->toJson());
});

it('can write skipped results to the database', function () {
    $this
        ->fakeDiskSpaceCheck
        ->everyFiveMinutes();

    artisan(RunHealthChecksCommand::class)->assertSuccessful();

    testTime()->addMinutes(4);

    artisan(RunHealthChecksCommand::class)->assertSuccessful();

    $report = app(ResultStore::class)->latestResults();

    expect($report->containsCheckWithStatus(Status::Skipped))->toBeTrue();
});
