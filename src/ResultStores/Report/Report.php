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
    protected Collection $reportedChecks;

    public static function fromJson(string $json): Report
    {
        $properties = json_decode($json, true);

        $reportedChecks = collect($properties['reportedChecks'])->map(
            fn(array $lineProperties) => new ReportedCheck(...$lineProperties)
        );

        return new static(
            finishedAt: new DateTime($properties['finishedAt']),
            reportedChecks: $reportedChecks,
        );
    }

    /**
     * @param \DateTimeInterface|null $finishedAt
     * @param array<int, ReportedCheck> $lines
     */
    public function __construct(
        DateTimeInterface $finishedAt = null,
        ?Collection $reportedChecks = null)
    {
        $this->finishedAt = $finishedAt ?? new DateTime();

        $this->reportedChecks = $reportedChecks ?? collect();
    }

    public function addCheck(ReportedCheck $line): self
    {
        $this->reportedChecks[] = $line;

        return $this;
    }

    public function allChecksOk(): bool
    {
        $this->reportedChecks->contains(
            fn(ReportedCheck $line) => $line->status !== Status::ok()->value
        );
    }

    public function containsFailingCheck(): bool
    {
        return ! $this->allChecksOk();
    }

    public function toJson(): string
    {
        return json_encode([
            'finishedAt' => $this->finishedAt->format('Y-m-d H:i:s'),
            'reportedChecks' => $this->reportedChecks->map(fn(ReportedCheck $line) => $line->toArray()),
        ]);
    }
}
