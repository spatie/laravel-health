<?php

namespace Spatie\Health\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Spatie\Health\Enums\Status;
use Spatie\Health\ResultStores\ResultStore;
use function Termwind\render;

class ListChecksCommand extends Command
{
    public $signature = 'health:list {--run}';

    public $description = 'List all health checks';

    public function handle(): int
    {
        if ($this->option('run')) {
            Artisan::call(RunChecksCommand::class);
        }

        $resultStore = app(ResultStore::class);

        $checkResults = $resultStore->latestResults();

        render(view('health::cli.list', [
            'lastRanAt' => new Carbon($checkResults?->finishedAt),
            'checkResults' => $checkResults,
            'color' => fn (string $status) => $this->getBackgroundColor($status),
        ]));

        return self::SUCCESS;
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
