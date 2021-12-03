<?php

namespace Spatie\Health\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Health\ResultStores\ResultStore;

class HealthCheckJsonResultsController
{
    public function __invoke(Request $request, ResultStore $resultStore): JsonResponse|View
    {
        $checkResults = $resultStore->latestResults();

        return response()->json($checkResults?->toJson() ?? []);
    }
}
