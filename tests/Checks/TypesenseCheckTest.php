
   
<?php

use Illuminate\Support\Facades\Http;
use Spatie\Health\Checks\Checks\TypesenseCheck;
use Spatie\Health\Enums\Status;

it('will determine that a working typesense is ok', function () {
    Http::fake([
        '*' => Http::response(['ok' => true]),
    ]);

    $result = TypesenseCheck::new()->run();

    expect($result->status)->toBe(Status::ok());
});

it('will determine that another status is not ok', function () {
    Http::fake([
        '*' => Http::response(['ok' => false]),
    ]);

    $result = TypesenseCheck::new()->run();

    expect($result->status)->toBe(Status::failed());
});

it('will determine that an http error is not ok', function () {
    Http::fake([
        '*' => Http::response(status: 500),
    ]);

    $result = TypesenseCheck::new()->run();

    expect($result->status)->toBe(Status::failed());
});
