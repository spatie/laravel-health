<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Health\Checks\Result;
use Spatie\Health\Enums\Status;
use Spatie\Health\Models\HealthCheckResultHistoryItem;
use Spatie\Health\ResultStores\CacheHealthResultStore;
use Spatie\Health\ResultStores\EloquentHealthResultStore;
use Spatie\Health\ResultStores\InMemoryHealthResultStore;
use Spatie\Health\ResultStores\JsonFileHealthResultStore;
use Spatie\Health\Tests\TestClasses\FakeUsedDiskSpaceCheck;

use function Spatie\PestPluginTestTime\testTime;

beforeEach(function () {
    testTime()->freeze('2021-01-01 00:00:00');
    Storage::fake('local');
    $this->legacyJson = file_get_contents(__DIR__.'/../Fixtures/legacy-health-results.json');
    $this->legacyResults = json_decode($this->legacyJson, true, flags: JSON_THROW_ON_ERROR);
});

it('round trips every status without changing the persistence format', function (string $storeClass) {
    $store = match ($storeClass) {
        CacheHealthResultStore::class => new CacheHealthResultStore('array'),
        JsonFileHealthResultStore::class => new JsonFileHealthResultStore('local', 'health.json'),
        default => new $storeClass,
    };

    $results = collect($this->legacyResults['checkResults'])->map(fn (array $row) => (new Result(
        Status::from($row['status']),
        $row['notificationMessage'],
        $row['shortSummary'],
    ))
        ->check(FakeUsedDiskSpaceCheck::new()->name($row['name'])->label($row['label']))
        ->meta($row['meta'])
        ->endedAt(now()));

    $store->save($results);

    $report = $store->latestResults();

    expect(json_decode($report->toJson(), true, flags: JSON_THROW_ON_ERROR))->toBe($this->legacyResults)
        ->and($report->storedCheckResults->pluck('status')->all())->toBe([
            Status::Ok, Status::Warning, Status::Failed, Status::Crashed, Status::Skipped,
        ]);

    if ($store instanceof CacheHealthResultStore) {
        expect(json_decode(cache()->store('array')->get('health:storeResults'), true))->toBe($this->legacyResults);
    }

    if ($store instanceof JsonFileHealthResultStore) {
        expect(json_decode(Storage::disk('local')->get('health.json'), true))->toBe($this->legacyResults);
    }

    if ($store instanceof EloquentHealthResultStore) {
        expect(DB::table('health_check_result_history_items')->pluck('status', 'check_name')->all())->toBe([
            'cache' => 'ok', 'disk' => 'warning', 'database' => 'failed', 'queue' => 'crashed', 'schedule' => 'skipped',
        ]);

        $item = HealthCheckResultHistoryItem::where('check_name', 'disk')->firstOrFail();

        expect($item->status)->toBe(Status::Warning)
            ->and($item->toArray()['status'])->toBe('warning')
            ->and(json_decode($item->toJson(), true)['status'])->toBe('warning');
    }
})->with([
    InMemoryHealthResultStore::class,
    CacheHealthResultStore::class,
    JsonFileHealthResultStore::class,
    EloquentHealthResultStore::class,
]);

it('reads all pre-enum persisted statuses without rewriting the data', function (string $storeClass) {
    $store = match ($storeClass) {
        CacheHealthResultStore::class => new CacheHealthResultStore('array'),
        JsonFileHealthResultStore::class => new JsonFileHealthResultStore('local', 'health.json'),
        default => new EloquentHealthResultStore,
    };

    if ($store instanceof CacheHealthResultStore) {
        cache()->store('array')->put('health:storeResults', $this->legacyJson);
    }

    if ($store instanceof JsonFileHealthResultStore) {
        Storage::disk('local')->put('health.json', $this->legacyJson);
    }

    if ($store instanceof EloquentHealthResultStore) {
        foreach ($this->legacyResults['checkResults'] as $row) {
            DB::table('health_check_result_history_items')->insert([
                'check_name' => $row['name'],
                'check_label' => $row['label'],
                'notification_message' => $row['notificationMessage'],
                'short_summary' => $row['shortSummary'],
                'status' => $row['status'],
                'meta' => json_encode($row['meta']),
                'batch' => 'c7b00e17-8451-4c6e-a742-39708df28e88',
                'ended_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    $report = $store->latestResults();

    expect(json_decode($report->toJson(), true, flags: JSON_THROW_ON_ERROR))->toBe($this->legacyResults);

    foreach ([Status::Ok, Status::Warning, Status::Failed, Status::Crashed, Status::Skipped] as $status) {
        expect($report->containsCheckWithStatus($status))->toBeTrue();
    }
})->with([
    CacheHealthResultStore::class,
    JsonFileHealthResultStore::class,
    EloquentHealthResultStore::class,
]);
