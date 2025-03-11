<?php

namespace Spatie\Health\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Spatie\Health\Commands\PauseHealthChecksCommand;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\ResultStores\ResultStore;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class SimpleHealthCheckController
{
    public function __invoke(Request $request, ResultStore $resultStore): Response
    {
        if (
            ($request->has('fresh') || config('health.oh_dear_endpoint.always_send_fresh_results'))
            && Cache::missing(PauseHealthChecksCommand::CACHE_KEY)
        ) {
            Artisan::call(RunHealthChecksCommand::class);
        }

        if (! ($resultStore->latestResults()?->allChecksOk())) {
            throw new ServiceUnavailableHttpException(message: 'Application not healthy');
        }

        return response([
            'healthy' => true,
        ])
            ->header('Content-Type', 'application/json')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    }
}
