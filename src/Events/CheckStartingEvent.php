<?php

namespace Spatie\Health\Events;

use Spatie\Health\Checks\Check;

class CheckStartingEvent
{
    public function __construct(public Check $check) {}
}
