<?php

namespace Spatie\Health\ResultStores;

use Illuminate\Support\Collection;
use OhDear\HealthCheckReport\Report;

interface ResultStore
{
    /** @param Collection<int, \Spatie\Health\Support\Result> $checkResults */
    public function save(Collection $checkResults): void;

    public function latestResults(): ?Report;
}
