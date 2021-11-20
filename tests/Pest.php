<?php

use Illuminate\Support\Facades\Mail;
use Spatie\Health\Tests\TestCase;

uses(TestCase::class)
    ->beforeEach(function () {
        Mail::fake();
    })
    ->in(__DIR__);

expect()->extend('between', function (int $min, $max) {
    expect($this->value)
        ->toBeGreaterThanOrEqual($min)
        ->toBeLessThanOrEqual($max);

    return $this;
});
