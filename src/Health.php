<?php

namespace Spatie\Health;

use \Spatie\Health\Checks\Check;

class Health
{
    /** @var array<int, Check>  */
    protected array $checks;

    /** @param array<int, Check> $checks */
    public function registerChecks(array $checks): self
    {
        $this->checks = array_merge($this->checks, $checks);

        return $this;
    }
}
