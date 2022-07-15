<?php

namespace Spatie\Health\ResultStores;

use Illuminate\Support\Collection;
use Spatie\Health\ResultStores\StoredCheckResults\StoredCheckResults;

interface ResultStore
{
    /** @param  Collection<int, \Spatie\Health\Checks\Result>  $checkResults */
    public function save(Collection $checkResults): void;

    public function latestResults(): ?StoredCheckResults;
}
