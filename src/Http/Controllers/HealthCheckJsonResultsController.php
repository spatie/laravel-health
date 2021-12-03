<?php

namespace Spatie\Health\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Health\Enums\Status;
use Spatie\Health\ResultStores\ResultStore;

class HealthCheckJsonResultsController
{
    public function __invoke(Request $request, ResultStore $resultStore): JsonResponse|View
    {
        $checkResults = $resultStore->latestResults();

        return response()->json($checkResults?->toJson() ?? []);
    }
}
