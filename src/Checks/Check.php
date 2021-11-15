<?php

namespace Spatie\Health\Checks;

use Cron\CronExpression;
use Illuminate\Console\Scheduling\ManagesFrequencies;
use Illuminate\Support\Facades\Date;
use Spatie\Health\Enums\Result;

abstract class Check
{
    protected string $expression = '* * * * *';

    use ManagesFrequencies;

    abstract public function name(): string;

    abstract public function result(): Result;

    abstract public function message(): ?string;

    public function shouldRunNow(): bool
    {
        $date = Date::now();

        return (new CronExpression($this->expression))->isDue($date->toDateTimeString());
    }

    public function meta(): array {
        return [];
    }
}
