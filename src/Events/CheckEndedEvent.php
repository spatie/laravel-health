<?php

namespace Spatie\Health\Events;

use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

class CheckEndedEvent
{
    public function __construct(
        public Check $check,
        public Result $result
    ) {}
}
