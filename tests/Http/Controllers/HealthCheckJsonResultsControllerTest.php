<?php

use Illuminate\Support\Facades\Route;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\Facades\Health;
use Spatie\Health\Http\Controllers\HealthCheckJsonResultsController;
use Spatie\Health\Http\Controllers\SimpleHealthCheckController;
use Spatie\Health\Http\Middleware\RequiresSecretToken;
use Spatie\Health\ResultStores\CacheHealthResultStore;
use Spatie\Health\ResultStores\ResultStore;
use Spatie\Health\ResultStores\StoredCheckResults\StoredCheckResults;
use Spatie\Health\Tests\TestClasses\FakeUsedDiskSpaceCheck;
use Symfony\Component\HttpFoundation\Response;

use function Pest\Laravel\artisan;
use function Pest\Laravel\getJson;
use function Spatie\PestPluginTestTime\testTime;
use function Spatie\Snapshots\assertMatchesSnapshot;

beforeEach(function () {
    testTime()->freeze('2021-01-01 00:00:00');

    Route::get('/', HealthCheckJsonResultsController::class);

    $this->check = FakeUsedDiskSpaceCheck::new()->fakeDiskUsagePercentage(100);

    Health::checks([
        $this->check,
    ]);
});

it('will display the results as json when the request accepts json', function () {
    artisan(RunHealthChecksCommand::class);

    $json = getJson('/')
        ->assertSuccessful()
        ->json();

    assertMatchesSnapshot($json);
});

it('will return 403 when secret token is defined and not send in header', function () {
    config()->set('health.secret_token', 'my-secret-token');

    Route::get('/', HealthCheckJsonResultsController::class)->middleware(RequiresSecretToken::class);

    getJson('/')
        ->assertForbidden();
});

it('will display the results when secret token is defined and send in header', function () {
    config()->set('health.secret_token', 'my-secret-token');

    Route::get('/', HealthCheckJsonResultsController::class)->middleware(RequiresSecretToken::class);

    getJson('/', ['X-Secret-Token' => 'my-secret-token'])
        ->assertSuccessful()
        ->json();
});

it('the output of the json endpoint can be used to create a StoredCheckResults object', function () {
    artisan(RunHealthChecksCommand::class);

    $jsonString = getJson('/')
        ->assertSuccessful()
        ->content();

    $storedCheckResults = StoredCheckResults::fromJson($jsonString);

    expect($storedCheckResults)->toBeInstanceOf(StoredCheckResults::class);
});

it('will run the checks when the run get parameter is passed and return the results as json', function () {
    $jsonString = getJson('/?fresh')
        ->assertSuccessful()
        ->content();

    $storedCheckResults = StoredCheckResults::fromJson($jsonString);

    expect($storedCheckResults)->toBeInstanceOf(StoredCheckResults::class)
        ->and($storedCheckResults->storedCheckResults)->toHaveCount(1);
});

it('will return the configured status code for an unhealthy check', function () {
    config()->set('health.json_results_failure_status', Response::HTTP_SERVICE_UNAVAILABLE);

    artisan(RunHealthChecksCommand::class);

    $json = getJson('/')
        ->assertServiceUnavailable()
        ->json();

    assertMatchesSnapshot($json);
});

it('will return http ok status code when there are no failing checks', function () {
    $this->check->fakeDiskUsagePercentage(50);

    config()->set('health.json_results_failure_status', Response::HTTP_SERVICE_UNAVAILABLE);

    artisan(RunHealthChecksCommand::class);

    getJson('/')
        ->assertOk();
});

it('preserves the JSON API and health decisions for persisted statuses', function (string $status, bool $failing, bool $treatSkippedAsFailure) {
    config()->set('health.json_results_failure_status', 503);
    config()->set('health.treat_skipped_as_failure', $treatSkippedAsFailure);
    app()->instance(ResultStore::class, new CacheHealthResultStore('array'));
    Route::get('/simple', SimpleHealthCheckController::class);

    $payload = [
        'finishedAt' => 1609459200,
        'checkResults' => [[
            'name' => 'custom',
            'label' => 'Custom check',
            'notificationMessage' => 'A message',
            'shortSummary' => 'A summary',
            'status' => $status,
            'meta' => ['count' => 7],
        ]],
    ];

    cache()->store('array')->put('health:storeResults', json_encode($payload, JSON_THROW_ON_ERROR));

    $expectedStatus = ($failing || ($status === 'skipped' && $treatSkippedAsFailure)) ? 503 : 200;

    getJson('/')->assertStatus($expectedStatus)->assertExactJson($payload);
    getJson('/simple')->assertStatus($expectedStatus);
})->with([
    ['ok', false],
    ['warning', true],
    ['failed', true],
    ['crashed', true],
    ['skipped', false],
])->with([true, false]);
