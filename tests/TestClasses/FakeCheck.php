<?php

namespace Spatie\Health\Tests\TestClasses;

use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;
use Spatie\Health\Enums\Status;

class FakeCheck extends Check
{
    public bool $hasRun = false;

    public function run(): Result
    {
        $this->hasRun = true;
        return new Result(Status::ok());
    }

}
