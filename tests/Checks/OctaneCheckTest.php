<?php

namespace Spatie\Health\Tests\Checks;

use Spatie\Health\Checks\Checks\CheckOctane;
use Spatie\Health\Enums\Status;

it('will fail when octane is not installed', function () {
    $result = CheckOctane::new()->run();

    expect($result)
        ->status->toBe(Status::failed())
        ->notificationMessage->toBe('Octane does not seem to be installed correctly.');
})->skip(fn () => extension_loaded('redis') !== true, 'The redis extension is not loaded.');

it('will determine that a running octane is ok', function () {
    $this->fakeOctaneStatus('running');

    $result = CheckOctane::new()->run();

    expect($result)->status->toBe(Status::ok());
});
