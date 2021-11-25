<?php

namespace Spatie\Health\ResultStores\Report;

use DateTime;
use DateTimeInterface;
use Illuminate\Support\Collection;
use Spatie\Health\Enums\Status;

class Report
{
    protected DateTimeInterface $finishedAt;

    /** @var Collection<int, ReportedCheck> */
    protected Collection $checkResults;

    public static function fromJson(string $json): Report
    {
        $properties = json_decode($json, true);

        $checkResults = collect($properties['checkResults'])->map(
            fn (array $lineProperties) => new ReportedCheck(...$lineProperties)
        );

        return new self(
            finishedAt: new DateTime($properties['finishedAt']),
            checkResults: $checkResults,
        );
    }

    /**
     * @param \DateTimeInterface|null $finishedAt
     * @param Collection<int, ReportedCheck> $checkResults
     */
    public function __construct(
        DateTimeInterface $finishedAt = null,
        ?Collection $checkResults = null
    ) {
        $this->finishedAt = $finishedAt ?? new DateTime();

        $this->checkResults = $checkResults ?? collect();
    }

    public function addCheck(ReportedCheck $line): self
    {
        $this->checkResults[] = $line;

        return $this;
    }

    public function allChecksOk(): bool
    {
        return $this->checkResults->contains(
            fn (ReportedCheck $line) => $line->status !== Status::ok()->value
        );
    }

    public function containsFailingCheck(): bool
    {
        return ! $this->allChecksOk();
    }

    public function toJson(): string
    {
        return (string)json_encode([
            'finishedAt' => $this->finishedAt->format('Y-m-d H:i:s'),
            'checkResults' => $this->checkResults->map(fn (ReportedCheck $line) => $line->toArray()),
        ]);
    }
}
