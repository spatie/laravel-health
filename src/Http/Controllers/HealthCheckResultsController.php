<?php

namespace Spatie\Health\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Health\ResultStores\ResultStore;

class HealthCheckResultsController
{
    public function __invoke(Request $request, ResultStore $resultStore): JsonResponse
    {
        $content = $resultStore->latestResults()?->toJson() ?? [];

        if ($request->acceptsJson()) {
            return response()->json($content);
        }
    }
}
