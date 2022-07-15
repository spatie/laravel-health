<?php

use Spatie\Health\Checks\Checks\OptimizedAppCheck;
use Spatie\Health\Enums\Status;

it('will check if services are cached', function () {
    $result = OptimizedAppCheck::new()->run();

    expect($result)->status->toBe(Status::failed());
});

it('can perform only selected checks', function () {
    $check = OptimizedAppCheck::new();

    expect($check->checks)->toBeNull();

    $check->checkRoutes();

    expect($check->checks)->toBe([OptimizedAppCheck::ROUTES]);
});

it('can perform all checks explicitly', function () {
    $check = OptimizedAppCheck::new();

    expect($check->checks)->toBeNull();

    $check
        ->checkRoutes()
        ->checkConfig()
        ->checkEvents();

    expect($check->checks)->toBe([
        OptimizedAppCheck::ROUTES,
        OptimizedAppCheck::CONFIG,
        OptimizedAppCheck::EVENTS,

    ]);
});
