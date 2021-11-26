<?php

namespace Spatie\Health\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Spatie\Health\Enums\Status;
use Spatie\Health\ResultStores\ResultStore;
use Spatie\Health\ResultStores\StoredCheckResults\StoredCheckResults;
use function Termwind\render;

class ListChecksCommand extends Command
{
    public $signature = 'health:list';

    public $description = 'List all health checks';

    public function handle(): int
    {
        $resultStore = app(ResultStore::class);

        /** @var StoredCheckResults $checkResults */
        $checkResults = $resultStore->latestResults();

        render(view('health::cli.list', [
            'lastRanAt' => new Carbon($checkResults->finishedAt),
            'checkResults' => $checkResults,
            'color' => function(string $status) {
                return $this->getBackgroundColor($status);
            },
        ]));

        return self::SUCCESS;
    }

    protected function getBackgroundColor(string $status)
    {
        return match($status) {
            Status::ok()->value => 'bg-green-800',
            Status::warning()->value => 'bg-orange-800',
            Status::skipped()->value => 'bg-blue-800',
            Status::failed()->value, Status::crashed()->value => 'bg-red-800',
            default => ''
        };
    }
}
