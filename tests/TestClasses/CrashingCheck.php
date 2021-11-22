<?php

namespace Spatie\Health\Tests\TestClasses;

use Exception;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

class CrashingCheck extends Check
{
    public function run(): Result
    {
        throw new Exception('This check will always crash');
    }
}
