<?php

namespace Spatie\Health\ResultStores;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Health\Models\CheckResultHistoryItem;
use Spatie\Health\Support\Result;

class EloquentHealthResultStore implements ResultStore
{
    /** @var Collection<int, Result> */
    public function save(Collection $checkResults): void
    {
        $batch = Str::uuid();

        $checkResults->each(function (Result $result) use ($batch) {
            CheckResultHistoryItem::create([
                'name' => $result->check->name(),
                'status' => $result->status,
                'message' => $result->getMessage(),
                'meta' => $result->meta,
                'batch' => $batch,
                'ended_at' => $result->ended_at,
            ]);
        });
    }
}
