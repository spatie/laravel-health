<?php

use Spatie\Health\Enums\Status;
use Spatie\Health\ResultStores\StoredCheckResults\StoredCheckResult;
use Spatie\Health\ResultStores\StoredCheckResults\StoredCheckResults;

it('has a method to check if the results contain a result with a certain status', function () {
    $storedCheckResults = new StoredCheckResults(new DateTime, collect([
        makeStoredCheckResultWithStatus(Status::Warning),
        makeStoredCheckResultWithStatus(Status::Ok),

    ]));

    expect($storedCheckResults->containsCheckWithStatus(Status::Ok))->toBeTrue();
    expect($storedCheckResults->containsCheckWithStatus(Status::Warning))->toBeTrue();
    expect($storedCheckResults->containsCheckWithStatus(Status::Failed))->toBeFalse();
    expect($storedCheckResults->containsCheckWithStatus(Status::Crashed))->toBeFalse();

    expect($storedCheckResults->containsCheckWithStatus([Status::Warning, Status::Failed]))->toBeTrue();
    expect($storedCheckResults->containsCheckWithStatus([Status::Crashed, Status::Failed]))->toBeFalse();
});

it('has a method to check if one or more checks are failing', function () {
    $storedCheckResults = new StoredCheckResults(new DateTime, collect([
        makeStoredCheckResultWithStatus(Status::Warning),
        makeStoredCheckResultWithStatus(Status::Ok),
    ]));
    expect($storedCheckResults->containsFailingCheck())->toBeTrue();
});

it('should not treat skipped checks as failing checks if treat_skipped_as_failure is disabled', function () {
    config()->set('health.treat_skipped_as_failure', false);
    $storedCheckResults = new StoredCheckResults(new DateTime, collect([
        makeStoredCheckResultWithStatus(Status::Skipped),
        makeStoredCheckResultWithStatus(Status::Ok),
    ]));
    expect($storedCheckResults->containsFailingCheck())->toBeFalse();
});

it('should treat skipped checks as failing checks if treat_skipped_as_failure is enabled', function () {
    config()->set('health.treat_skipped_as_failure', true);
    $storedCheckResults = new StoredCheckResults(new DateTime, collect([
        makeStoredCheckResultWithStatus(Status::Skipped),
        makeStoredCheckResultWithStatus(Status::Ok),
    ]));
    expect($storedCheckResults->containsFailingCheck())->toBeTrue();
});

it('should treat skipped checks as failing checks if treat_skipped_as_failure is not defined', function () {
    $storedCheckResults = new StoredCheckResults(new DateTime, collect([
        makeStoredCheckResultWithStatus(Status::Skipped),
        makeStoredCheckResultWithStatus(Status::Ok),
    ]));
    expect($storedCheckResults->containsFailingCheck())->toBeTrue();
});

it('has a method to check if all checks are good', function () {
    $storedCheckResults = new StoredCheckResults(new DateTime, collect([
        makeStoredCheckResultWithStatus(Status::Ok),
        makeStoredCheckResultWithStatus(Status::Ok),
    ]));
    expect($storedCheckResults->allChecksOk())->toBeTrue();
});

it('does not report a result without an explicit status as healthy', function () {
    $results = new StoredCheckResults(checkResults: collect([
        StoredCheckResult::make('unspecified'),
        new StoredCheckResult('also unspecified'),
    ]));

    expect($results->allChecksOk())->toBeFalse();
});

it('treats missing and unrecognized persisted statuses as failed', function (array $properties) {
    $results = StoredCheckResults::fromJson(json_encode([
        'finishedAt' => 1609459200,
        'checkResults' => [array_merge(['name' => 'legacy'], $properties)],
    ], JSON_THROW_ON_ERROR));

    expect($results->storedCheckResults->sole()->status)->toBe(Status::Failed)
        ->and($results->containsCheckWithStatus(Status::Failed))->toBeTrue()
        ->and($results->allChecksOk())->toBeFalse();
})->with([
    'missing' => [[]],
    'empty' => [['status' => '']],
    'unknown' => [['status' => 'custom']],
    'null' => [['status' => null]],
]);

function makeStoredCheckResultWithStatus(Status $status): StoredCheckResult
{
    return StoredCheckResult::make(
        'test',
        status: $status,
    );
}
