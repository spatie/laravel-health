<?php

namespace Spatie\Health\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\ResultStores\ResultStore;

class HealthCheckJsonResultsController
{
    public function __invoke(Request $request, ResultStore $resultStore): JsonResponse
    {
        if ($request->has('fresh') || config('health.oh_dear_endpoint.always_send_fresh_results')) {
            Artisan::call(RunHealthChecksCommand::class);
        }

        $checkResults = $resultStore->latestResults();

        return response()
            ->json($checkResults?->toArray()??[])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    }
}
