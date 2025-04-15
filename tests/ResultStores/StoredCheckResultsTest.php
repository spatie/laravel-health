<?php

use Spatie\Health\Enums\Status;
use Spatie\Health\ResultStores\StoredCheckResults\StoredCheckResult;
use Spatie\Health\ResultStores\StoredCheckResults\StoredCheckResults;

it('has a method to check if the results contain a result with a certain status', function () {
    $storedCheckResults = new StoredCheckResults(new DateTime, collect([
        makeStoredCheckResultWithStatus(Status::warning()),
        makeStoredCheckResultWithStatus(Status::ok()),

    ]));

    expect($storedCheckResults->containsCheckWithStatus(Status::ok()))->toBeTrue();
    expect($storedCheckResults->containsCheckWithStatus(Status::warning()))->toBeTrue();
    expect($storedCheckResults->containsCheckWithStatus(Status::failed()))->toBeFalse();
    expect($storedCheckResults->containsCheckWithStatus(Status::crashed()))->toBeFalse();

    expect($storedCheckResults->containsCheckWithStatus([Status::warning(), Status::failed()]))->toBeTrue();
    expect($storedCheckResults->containsCheckWithStatus([Status::crashed(), Status::failed()]))->toBeFalse();
});

it('has a method to check if one or more checks are failing', function () {
    $storedCheckResults = new StoredCheckResults(new DateTime, collect([
        makeStoredCheckResultWithStatus(Status::warning()),
        makeStoredCheckResultWithStatus(Status::ok()),
    ]));
    expect($storedCheckResults->containsFailingCheck())->toBeTrue();
});

it('should not treat skipped checks as failing checks if treat_skipped_as_failure is disabled', function () {
    config()->set('health.treat_skipped_as_failure', false);
    $storedCheckResults = new StoredCheckResults(new DateTime, collect([
        makeStoredCheckResultWithStatus(Status::skipped()),
        makeStoredCheckResultWithStatus(Status::ok()),
    ]));
    expect($storedCheckResults->containsFailingCheck())->toBeFalse();
});

it('should treat skipped checks as failing checks if treat_skipped_as_failure is enabled', function () {
    config()->set('health.treat_skipped_as_failure', true);
    $storedCheckResults = new StoredCheckResults(new DateTime, collect([
        makeStoredCheckResultWithStatus(Status::skipped()),
        makeStoredCheckResultWithStatus(Status::ok()),
    ]));
    expect($storedCheckResults->containsFailingCheck())->toBeTrue();
});

it('should treat skipped checks as failing checks if treat_skipped_as_failure is not defined', function () {
    $storedCheckResults = new StoredCheckResults(new DateTime, collect([
        makeStoredCheckResultWithStatus(Status::skipped()),
        makeStoredCheckResultWithStatus(Status::ok()),
    ]));
    expect($storedCheckResults->containsFailingCheck())->toBeTrue();
});

it('has a method to check if all checks are good', function () {
    $storedCheckResults = new StoredCheckResults(new DateTime, collect([
        makeStoredCheckResultWithStatus(Status::ok()),
        makeStoredCheckResultWithStatus(Status::ok()),
    ]));
    expect($storedCheckResults->allChecksOk())->toBeTrue();
});

function makeStoredCheckResultWithStatus(Status $status): StoredCheckResult
{
    return StoredCheckResult::make(
        'test',
        status: $status,
    );
}
