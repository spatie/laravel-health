<?php

namespace Spatie\Health\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Spatie\Health\Enums\Status;
use Spatie\Health\ResultStores\ResultStore;
use Spatie\Health\ResultStores\StoredCheckResults\StoredCheckResult;
use Spatie\Health\ResultStores\StoredCheckResults\StoredCheckResults;

use function Termwind\render;
use function Termwind\renderUsing;

class ListHealthChecksCommand extends Command
{
    protected $signature = 'health:list {--fresh} {--do-not-store-results} {--no-notification}
                         {--fail-command-on-failing-check}';

    protected $description = 'List all health checks';

    public function handle(): int
    {
        if ($this->option('fresh')) {
            $parameters = [];
            if ($this->option('do-not-store-results')) {
                $parameters['--do-not-store-results'] = true;
            }
            if ($this->option('no-notification')) {
                $parameters['--no-notification'] = true;
            }

            Artisan::call(RunHealthChecksCommand::class, $parameters);
        }

        $resultStore = app(ResultStore::class);

        $checkResults = $resultStore->latestResults();

        renderUsing($this->output);
        render(view('health::list-cli', [
            'lastRanAt' => new Carbon($checkResults?->finishedAt),
            'checkResults' => $checkResults,
            'color' => fn (string $status) => $this->getBackgroundColor($status),
        ]));

        return $this->determineCommandResult($checkResults);
    }

    protected function getBackgroundColor(string $status): string
    {
        $status = Status::from($status);

        return match ($status) {
            Status::ok() => 'text-green-600',
            Status::warning() => 'text-yellow-600',
            Status::skipped() => 'text-blue-600',
            Status::failed(), Status::crashed() => 'text-red-600',
            default => ''
        };
    }

    protected function determineCommandResult(?StoredCheckResults $results): int
    {
        if (! $this->option('fail-command-on-failing-check') || is_null($results)) {
            return self::SUCCESS;
        }

        $containsFailingCheck = $results->storedCheckResults->contains(function (StoredCheckResult $result) {
            return in_array($result->status, [
                Status::crashed(),
                Status::failed(),
                Status::warning(),
            ]);
        });

        return $containsFailingCheck
            ? self::FAILURE
            : self::SUCCESS;
    }
}
