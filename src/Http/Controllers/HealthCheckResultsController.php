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

        return view('health::list', [
            'lastRanAt' => new Carbon($checkResults?->finishedAt),
            'backgroundColor' => fn(string $status) => $this->getBackgroundColor($status),
            'textColor' => fn(string $status) => $this->getTextColor($status),
            'checkResults' => $checkResults
        ]);
    }

    protected function getBackgroundColor(string $status): string
    {
        return match ($status) {
            Status::ok()->value => 'bg-green-200',
            Status::warning()->value => 'bg-yellow-200',
            Status::skipped()->value => 'bg-blue-200',
            Status::failed()->value, Status::crashed()->value => 'bg-red-200',
            default => ''
        };
    }

    protected function getTextColor(string $status): string
    {
        return match ($status) {
            Status::ok()->value => 'text-green-900',
            Status::warning()->value => 'text-yellow-900',
            Status::skipped()->value => 'text-blue-900',
            Status::failed()->value, Status::crashed()->value => 'text-red-900',
            default => ''
        };
    }
}
