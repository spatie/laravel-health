<?php

namespace Spatie\Health;

use Illuminate\Support\Collection;
use Spatie\Health\Checks\Check;
use Spatie\Health\ResultStores\ResultStores;
use Spatie\Health\ResultStores\ResultStore;

class Health
{
    /** @var array<int, Check>  */
    protected array $checks = [];

    /** @param array<int, Check> $checks */
    public function checks(array $checks): self
    {
        $this->checks = array_merge($this->checks, $checks);

        return $this;
    }

    public function clearChecks(): self
    {
        $this->checks = [];

        return $this;
    }

    /** @return Collection<int, Check> */
    public function registeredChecks(): Collection
    {
        return collect($this->checks);
    }

    /** @return Collection<int, ResultStore> */
    public function resultStores(): Collection
    {
        return ResultStores::createFromConfig(config('health.result_stores'));
    }
}
