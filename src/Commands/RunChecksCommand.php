<?php

namespace Spatie\Health\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;
use Spatie\Health\Events\CheckEndedEvent;
use Spatie\Health\Events\CheckStartingEvent;
use Spatie\Health\Exceptions\CheckDidNotComplete;
use Spatie\Health\Exceptions\CouldNotSaveResultsInStore;
use Spatie\Health\Health;
use Spatie\Health\Notifications\CheckFailedNotification;
use Spatie\Health\ResultStores\ResultStore;

class RunChecksCommand extends Command
{
    public $signature = 'health:run-checks';

    public $description = 'Run all health checks';

    /** @var array<int, Exception> */
    protected array $thrownExceptions = [];

    public function handle(): int
    {
        $results = $this->runChecks();

        $this->sendNotification($results);

        if (count($this->thrownExceptions)) {
            foreach ($this->thrownExceptions as $exception) {
                $this->error($exception->getMessage());
            }

            return self::FAILURE;
        }

        $this->comment('All done');

        return self::SUCCESS;
    }

    public function runCheck(Check $check): Result
    {
        event(new CheckStartingEvent($check));

        try {
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

        event(new CheckEndedEvent($check, $result));

        return $result;
    }

    public function saveResults(Collection $results, ResultStore $store): void
    {
        try {
            $store->save($results);
        } catch (Exception $exception) {
            $exception = CouldNotSaveResultsInStore::make($store, $exception);

            report($exception);
        }
    }

    /** @return Collection<int, Result> */
    protected function runChecks(): Collection
    {
        $results = app(Health::class)
            ->registeredChecks()
            ->filter(fn (Check $check) => $check->shouldRun())
            ->map(fn (Check $check) => $this->runCheck($check));

        app(Health::class)
            ->resultStores()
            ->each(fn (ResultStore $store) => $store->save($results));

        return $results;
    }

    protected function sendNotification(Collection $results): self
    {
        $resultsWithMessages = $results->filter(fn (Result $result) => ! empty($result->getMessage()));

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
}
