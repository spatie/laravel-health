<?php

namespace Spatie\Health\ResultStores;

use Illuminate\Support\Collection;

interface ResultStore
{
    /** @param Collection<int, \Spatie\Health\Support\Result> */
    public function save(Collection $checkResults): void;
}
