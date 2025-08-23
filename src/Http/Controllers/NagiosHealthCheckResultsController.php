<?php

namespace Spatie\Health\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\ResultStores\ResultStore;
use Spatie\Health\ResultFormats\NagiosResultsFormat;

class NagiosHealthCheckResultsController
{
    public function __invoke(Request $request, ResultStore $resultStore): Response
    {
        if ($request->has('fresh') || config('health.nagios_endpoint.always_send_fresh_results')) {
            Artisan::call(RunHealthChecksCommand::class);
        }

        $checkResults = $resultStore->latestResults();

        if ($checkResults === null) {
            return response("UNKNOWN: No health check results available", 200)
                ->header('Content-Type', 'text/plain');
        }

        $formatter = new NagiosResultsFormat();
        return response($formatter->format($checkResults))
            ->header('Content-Type', 'text/plain');
    }
}
