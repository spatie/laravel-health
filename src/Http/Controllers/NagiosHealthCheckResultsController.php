<?php

namespace Spatie\Health\Http\Controllers;

use Illuminate\Http\Response;
use Spatie\Health\ResultStores\ResultStore;
use Spatie\Health\ResultFormats\NagiosResultsFormat;

class NagiosHealthCheckResultsController
{
    public function __invoke(ResultStore $resultStore): Response
    {
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
