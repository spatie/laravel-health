<?php

namespace Spatie\Health\Checks;

use Cron\CronExpression;
use Illuminate\Console\Scheduling\ManagesFrequencies;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use Spatie\Health\Enums\Status;
use Spatie\Health\Support\Result;

abstract class Check
{
    protected string $expression = '* * * * *';

    use ManagesFrequencies;

    public static function new(): static
    {
        $instance = new static();

        $instance->everyMinute();

        return $instance;
    }

    public function name(): string
    {
        $baseName =  class_basename(static::class);

        return Str::of($baseName)->beforeLast('Check');
    }

    public function shouldRun(): bool
    {
        $date = Date::now();

        return (new CronExpression($this->expression))->isDue($date->toDateTimeString());
    }

    abstract public function run(): Result;

    public function markAsCrashed()
    {
        return new Result(Status::crashed());
    }
}
