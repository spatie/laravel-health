<?php

namespace Spatie\Health\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\ResultStores\ResultStore;

class HealthCheckJsonResultsController
{
    public function __invoke(Request $request, ResultStore $resultStore): Response
    {
        if (
            config('health.secret_token')
            && ($request->header('X-Secret-Token') !== config('health.secret_token'))
        ) {
            return response(null, Response::HTTP_UNAUTHORIZED)
                ->header('Content-Type', 'application/json')
                ->header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        }

        if ($request->has('fresh') || config('health.oh_dear_endpoint.always_send_fresh_results')) {
            Artisan::call(RunHealthChecksCommand::class);
        }

        $checkResults = $resultStore->latestResults();

        $statusCode = $checkResults?->containsFailingCheck()
            ? config('health.json_results_failure_status', Response::HTTP_OK)
            : Response::HTTP_OK;

        return response($checkResults?->toJson() ?? '', $statusCode)
            ->header('Content-Type', 'application/json')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    }
}
