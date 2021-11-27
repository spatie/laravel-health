<?php

namespace Spatie\Health\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;
use Spatie\Health\Enums\Status;
use Spatie\Health\Events\CheckEndedEvent;
use Spatie\Health\Events\CheckStartingEvent;
use Spatie\Health\Exceptions\CheckDidNotComplete;
use Spatie\Health\Health;
use Spatie\Health\Notifications\CheckFailedNotification;
use Spatie\Health\ResultStores\ResultStore;

class RunHealthChecksCommand extends Command
{
    public $signature = 'health:run {--do-not-store-results} {--no-notification}';

    public $description = 'Run all health checks';

    /** @var array<int, Exception> */
    protected array $thrownExceptions = [];

    public function handle(): int
    {
        $this->info('Running checks...');

        $results = $this->runChecks();

        if (! $this->option('do-not-store-results')) {
            $this->storeResults($results);
        }

        if (! $this->option('no-notification')) {
            $this->sendNotification($results);
        }

        $this->line('');
        $this->info('All done!');

        return $this->determineCommandResult($results);
    }

    public function runCheck(Check $check): Result
    {
        event(new CheckStartingEvent($check));

        try {
            $this->line('');
            $this->line("Running check: {$check->getLabel()}...");
            $result = $check->run();
        } catch (Exception $exception) {
            $exception = CheckDidNotComplete::make($check, $exception);
            report($exception);

            $this->thrownExceptions[] = $exception;

            $result = $check->markAsCrashed();
        }

        $result
            ->check($check)
            ->endedAt(now());

        $this->outputResult($result, $exception ?? null);

        event(new CheckEndedEvent($check, $result));

        return $result;
    }

    /** @return Collection<int, Result> */
    protected function runChecks(): Collection
    {
        return app(Health::class)
            ->registeredChecks()
            ->map(function (Check $check): Result {
                return $check->shouldRun()
                    ? $this->runCheck($check)
                    : (new Result(Status::skipped()))->check($check);
            });
    }

    /** @param Collection<int, Result> $results */
    protected function storeResults(Collection $results): self
    {
        app(Health::class)
            ->resultStores()
            ->each(fn (ResultStore $store) => $store->save($results));

        return $this;
    }

    protected function sendNotification(Collection $results): self
    {
        $resultsWithMessages = $results->filter(fn (Result $result) => ! empty($result->getNotificationMessage()));

        if ($resultsWithMessages->count() === 0) {
            return $this;
        }

        $notifiableClass = config('health.notifications.notifiable');

        /** @var \Spatie\Health\Notifications\Notifiable $notifiable */

        $notifiable = app($notifiableClass);

        /** @var array<int, Result> $results */
        $results = $resultsWithMessages->toArray();

        $notification = (new CheckFailedNotification($results));

        $notifiable->notify($notification);

        return $this;
    }

    protected function outputResult(Result $result, ?Exception $exception = null): void
    {

        $status = ucfirst((string)$result->status->value);

        $okMessage = $status;

        if (! empty($result->shortSummary)) {
            $okMessage .= ": {$result->shortSummary}";
        }

        match ($result->status->value) {
            Status::ok()->value => $this->info($okMessage),
            Status::warning()->value => $this->comment("{$status}: $result->notificationMessage"),
            Status::failed()->value => $this->error("{$status}: $result->notificationMessage"),
            Status::crashed()->value => $this->error("{$status}}: `{$exception?->getMessage()}`"),
            default => null,
        };
    }

    protected function determineCommandResult(Collection $results): int
    {
        if (count($this->thrownExceptions)) {
            return static::FAILURE;
        }

        $allChecksOk = $results->contains(function(Result $result) {
            return in_array($result->status->value, [
                Status::crashed()->value,
                Status::failed()->value,
                Status::warning()->value,
            ]);
        });

        return $allChecksOk
            ? static::SUCCESS
            : static::FAILURE;
    }
}
