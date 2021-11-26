<?php

namespace Spatie\Health\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Spatie\Health\ResultStores\ResultStore;

class JsonSummaryController
{
    public function __invoke(ResultStore $resultStore): JsonResponse
    {
        $latestResultsCollection = $resultStore->latestResults();

        $status = $latestResultsCollection?->allChecksOk()
            ? 200
            : 400;

        $content = $resultStore->latestResults()?->toJson() ?? [];

        return response()->json($content, $status);
    }
}
