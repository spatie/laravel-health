<?php

use Spatie\Health\ResultStores\StoredCheckResults\StoredCheckResults;
use Spatie\Health\ResultStores\StoredCheckResults\StoredCheckResult;
use function Spatie\Snapshots\assertMatchesSnapshot;

it('can create create report', function () {
    $report = getReport();

    assertMatchesSnapshot($report->toJson());
});

it('can be created from json', function () {
    $json = getReport()->toJson();

    $newReport = StoredCheckResults::fromJson($json);

    expect($newReport->toJson())->toBe($json);
});

function getReport(): StoredCheckResults
{
    $checkResults = collect([
        new StoredCheckResult(
            'name',
            'message',
            'ok',
            ['name' => 'value']
        ),
    ]);

    return new StoredCheckResults(
        finishedAt: new DateTimeImmutable('2001-01-01 00:00:00'),
        checkResults: $checkResults,
    );
}
