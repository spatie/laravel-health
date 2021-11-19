<?php

namespace Spatie\Health\Http\Controllers;

use Spatie\Health\ResultStores\ResultStore;

class OhDearHealthSummaryController
{
    public function __invoke(ResultStore $resultStore)
    {
        return response()->json($resultStore->latestResults()->toJson());
    }
}
