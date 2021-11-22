<?php

namespace Spatie\Health\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Spatie\Health\ResultStores\ResultStore;

class JsonSummaryController
{
    public function __invoke(ResultStore $resultStore): JsonResponse
    {
        $report = $resultStore->latestReport();

        $status = $report?->allChecksOk()
            ? 200
            : 400;

        $content = $resultStore->latestReport()?->toJson() ?? [];

        return response(status: $status)->json($content);
    }
}
