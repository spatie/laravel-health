<?php

namespace Spatie\Health\Events;

use Spatie\Health\Checks\Check;
use Spatie\Health\Support\Result;

class CheckEndedEvent
{
    public function __construct(
        public Check $check,
        public Result $result
    ) {
    }
}
