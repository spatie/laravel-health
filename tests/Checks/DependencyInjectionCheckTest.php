<?php

use Psr\Log\LoggerInterface;
use Spatie\Health\Tests\TestClasses\DependencyInjectionCheck;

it('will use dependency injection to resolve constructor arguments', function () {
    $check = DependencyInjectionCheck::new();

    expect($check->getLogger())->toBeInstanceOf(LoggerInterface::class);
});
