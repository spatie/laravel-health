<?php

namespace Spatie\Health\Http\Controllers;

use Illuminate\Contracts\View\View;
use Spatie\Health\ResultStores\ResultStore;

class SummaryController
{
    public function __invoke(ResultStore $resultStore): View
    {
        $report = $resultStore->latestResults();

        return view('health::summary', compact('report'));
    }
}
