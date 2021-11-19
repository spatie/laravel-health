<?php

namespace Spatie\Health\OhDear;

use DateTime;
use DateTimeInterface;

class Run
{
    protected DateTime $finishedAt;

    /** @var array<int, CheckResult> */
    protected array $checks;

    /**
     * @param \DateTimeInterface|null $finishedAt
     * @param array<int, \Spatie\Health\OhDear\CheckResult> $checkResults
     */
    public function __construct(DateTimeInterface $finishedAt = null, array $checkResults = [])
    {
        $this->finishedAt = $finishedAt ?? new DateTime();

        $this->checks = $checkResults;
    }

    public function addCheck(CheckResult $check)
    {
        $this->checks[] = $check;
    }

    public function toJson(): string
    {
        $checkProperties = array_map(fn(CheckResult $checkResult) => $checkResult->toArray(), $this->checks);

        return json_encode([
            'finishedAt' => $this->finishedAt,
            'checks' => $checkProperties,
        ]);
    }
}
