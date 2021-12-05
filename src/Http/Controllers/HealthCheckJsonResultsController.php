<?php

namespace Spatie\Health\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\Health\ResultStores\ResultStore;

class HealthCheckJsonResultsController
{
    public function __invoke(Request $request, ResultStore $resultStore): Response
    {
        $checkResults = $resultStore->latestResults();

        return response($checkResults?->toJson() ?? '');
    }
}
