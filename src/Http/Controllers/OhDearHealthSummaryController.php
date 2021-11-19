<?php

namespace Spatie\Health\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Spatie\Health\ResultStores\ResultStore;

class OhDearHealthSummaryController
{
    public function __invoke(ResultStore $resultStore): JsonResponse
    {
        return response()->json($resultStore->latestResults()?->toJson() ?? []);
    }
}
