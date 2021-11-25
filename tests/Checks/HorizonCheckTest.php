<?php

namespace Spatie\Health\Tests\Checks;

use Spatie\Health\Checks\Checks\HorizonCheck;
use Spatie\Health\Enums\Status;

beforeEach()->markTestSkipped();

it('will fail when horizon is not running', function () {
    $result = HorizonCheck::new()->run();

    expect($result)
        ->status->toBe(Status::failed())
        ->message->toBe('Horizon is not running.');
});

it('will send a warning when horizon is paused', function () {
    $this->fakeHorizonStatus('paused');

    $result = HorizonCheck::new()->run();

    expect($result)
        ->status->toBe(Status::warning())
        ->message->toBe('Horizon is running, but the status is paused.');
});

it('will determine that a running horizon is ok', function () {
    $this->fakeHorizonStatus('running');

    $result = HorizonCheck::new()->run();

    expect($result)
        ->status->toBe(Status::ok());
});
