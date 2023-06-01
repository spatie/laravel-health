<?php

use Illuminate\Support\Facades\Http;
use Spatie\Health\Checks\Checks\FlareErrorOccurrenceCountCheck;
use Spatie\Health\Enums\Status;

beforeEach(function () {
    $this->check = FlareErrorOccurrenceCountCheck::new()
        ->projectId(1)
        ->apiToken('fake-token')
        ->periodInMinutes(60)
        ->warnWhenMoreErrorsReceivedThan(10)
        ->failWhenMoreErrorsReceivedThan(20);
});

it('can check the error occurrence count in flare', function (int $actualErrorCount, Status $expectedStatus) {
    Http::fake([
        '*' => ['count' => $actualErrorCount],
    ]);

    $result = $this->check->run();

    expect($result->status->value)->toBe($expectedStatus->value);
})->with([
    [0, Status::ok()],
    [10, Status::ok()],
    [11, Status::warning()],
    [20, Status::warning()],
    [21, Status::failed()],
]);
