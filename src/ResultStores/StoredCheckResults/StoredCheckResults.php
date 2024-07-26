<?php

namespace Spatie\Health\ResultStores\StoredCheckResults;

use DateTime;
use DateTimeInterface;
use Illuminate\Support\Collection;
use Spatie\Health\Enums\Status;

class StoredCheckResults
{
    public DateTimeInterface $finishedAt;

    /** @var Collection<int, StoredCheckResult> */
    public Collection $storedCheckResults;

    public static function fromJson(string $json): StoredCheckResults
    {
        $properties = json_decode($json, true);

        $checkResults = collect($properties['checkResults'])
            ->map(fn (array $lineProperties) => new StoredCheckResult(
                $lineProperties['name'],
                $lineProperties['label'] ?? '',
                $lineProperties['notificationMessage'] ?? '',
                $lineProperties['shortSummary'] ?? '',
                $lineProperties['status'] ?? '',
                $lineProperties['meta'] ?? [],
            ))
            ->unique('name')
            ->sortBy(fn (StoredCheckResult $result) => strtolower($result->label));

        return new self(
            finishedAt: (new DateTime)->setTimestamp($properties['finishedAt']),
            checkResults: $checkResults,
        );
    }

    /**
     * @param  ?Collection<int, StoredCheckResult>  $checkResults
     */
    public function __construct(
        ?DateTimeInterface $finishedAt = null,
        ?Collection $checkResults = null
    ) {
        $this->finishedAt = $finishedAt ?? new DateTime;

        $this->storedCheckResults = $checkResults ?? collect();
    }

    public function addCheck(StoredCheckResult $line): self
    {
        $this->storedCheckResults[] = $line;

        return $this;
    }

    public function allChecksOk(): bool
    {
        return ! $this->containsFailingCheck();
    }

    public function containsFailingCheck(): bool
    {
        return $this->storedCheckResults->contains(
            fn (StoredCheckResult $line) => $line->status !== Status::ok()->value
        );
    }

    /**
     * @param  array<int, Status>|Status  $statuses
     */
    public function containsCheckWithStatus(array|Status $statuses): bool
    {
        if ($statuses instanceof Status) {
            $statuses = [$statuses];
        }

        return $this->storedCheckResults->contains(
            fn (StoredCheckResult $line) => in_array($line->status, $statuses)
        );
    }

    public function toJson(): string
    {
        return (string) json_encode([
            'finishedAt' => $this->finishedAt->getTimestamp(),
            'checkResults' => $this->storedCheckResults->map(fn (StoredCheckResult $line) => $line->toArray()),
        ]);
    }
}
