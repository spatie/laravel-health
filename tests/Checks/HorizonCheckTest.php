<?php

namespace Spatie\Health\Tests\Checks;

use Spatie\Health\Checks\Checks\HorizonCheck;
use Spatie\Health\Enums\Status;

it('will fail when horizon is not running', function () {
    $result = HorizonCheck::new()->run();

    expect($result)
        ->status->toBe(Status::failed())
        ->notificationMessage->toBe('Horizon is not running.');
})->skip(fn () => extension_loaded('redis') !== true, 'The redis extension is not loaded.');

it('will send a warning when horizon is paused', function () {
    $this->fakeHorizonStatus('paused');

    $result = HorizonCheck::new()->run();

    expect($result)
        ->status->toBe(Status::warning())
        ->notificationMessage->toBe('Horizon is running, but the status is paused.');
});

it('will determine that a running horizon is ok', function () {
    $this->fakeHorizonStatus('running');

    $result = HorizonCheck::new()->run();

    expect($result)->status->toBe(Status::ok());
});
