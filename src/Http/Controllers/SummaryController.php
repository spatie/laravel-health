<?php

namespace Spatie\Health\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Spatie\Health\ResultStores\ResultStore;

class SummaryController
{
    public function __invoke(ResultStore $resultStore): JsonResponse
    {
        $report = $resultStore->latestReport();

        return view('health::summary', compact('report'));
    }
}
