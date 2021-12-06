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
        if ($request->has('run')) {
            Artisan::call(RunHealthChecksCommand::class);
        }

        $checkResults = $resultStore->latestResults();

        return response($checkResults?->toJson() ?? '');
    }
}
