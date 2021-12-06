<?php

namespace Spatie\Health\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Spatie\Health\Enums\Status;
use Spatie\Health\ResultStores\ResultStore;
use function Termwind\render;

class ListHealthChecksCommand extends Command
{
    public $signature = 'health:list {--run} {--do-not-store-results} {--no-notification}';

    public $description = 'List all health checks';

    public function handle(): int
    {
        if ($this->option('run')) {
            $parameters = [];
            if ($this->option('do-not-store-results')) {
                $parameters[] = '--do-not-store-results';
            }
            if ($this->option('--no-notification')) {
                $parameters[] = '--no-notification';
            }

            Artisan::call(RunHealthChecksCommand::class, $parameters);
        }

        $resultStore = app(ResultStore::class);

        $checkResults = $resultStore->latestResults();

        render(view('health::list-cli', [
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
            Status::warning()->value => 'bg-yellow-800',
            Status::skipped()->value => 'bg-blue-800',
            Status::failed()->value, Status::crashed()->value => 'bg-red-800',
            default => ''
        };
    }
}
