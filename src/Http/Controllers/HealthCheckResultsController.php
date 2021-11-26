<?php

namespace Spatie\Health\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Health\Enums\Status;
use Spatie\Health\ResultStores\ResultStore;

class HealthCheckResultsController
{
    public function __invoke(Request $request, ResultStore $resultStore): JsonResponse|View
    {
        $checkResults = $resultStore->latestResults();

        if ($request->acceptsHtml()) {
            return view('health::list', [
                'lastRanAt' => new Carbon($checkResults?->finishedAt),
                'color' => fn (string $status) => $this->getBackgroundColor($status),
                'checkResults' => $checkResults,
            ]);
        }

        return response()->json($checkResults?->toJson() ?? []);
    }

    protected function getBackgroundColor(string $status): string
    {
        return match ($status) {
            Status::ok()->value => 'bg-green-800',
            Status::warning()->value => 'bg-orange-800',
            Status::skipped()->value => 'bg-blue-800',
            Status::failed()->value, Status::crashed()->value => 'bg-red-800',
            default => ''
        };
    }
}
