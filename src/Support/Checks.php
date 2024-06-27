<?php

namespace Spatie\Health\Support;

use Illuminate\Support\Collection;

class Checks extends Collection
{
    /** @param  array<int, \Spatie\Health\Checks\Check>  $checks */
    public function __construct(array $checks)
    {
        parent::__construct($checks);
    }

    public function run(): void {}
}
