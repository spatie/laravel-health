<?php

use Spatie\Health\Checks\Checks\CachedServicesCheck;
use Spatie\Health\Enums\Status;

it('will check if services are cached', function () {
    $result = CachedServicesCheck::new()
        ->run();

    expect($result)
        ->status->toBe(Status::failed());
});
