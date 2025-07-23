<?php

use Illuminate\Support\Facades\Http;
use Spatie\Health\Checks\Checks\MeilisearchCheck;
use Spatie\Health\Enums\Status;

it('will determine that a working meilisearch is ok', function () {
    Http::fake([
        '*' => Http::response(['status' => 'available']),
    ]);

    $result = MeilisearchCheck::new()->run();

    expect($result->status)->toBe(Status::ok());
});

it('will determine that another status is not ok', function () {
    Http::fake([
        '*' => Http::response(['status' => 'not ok']),
    ]);

    $result = MeilisearchCheck::new()->run();

    expect($result->status)->toBe(Status::failed());
});

it('will determine that an http error is not ok', function () {
    Http::fake([
        '*' => Http::response(status: 500),
    ]);

    $result = MeilisearchCheck::new()->run();

    expect($result->status)->toBe(Status::failed());
});
