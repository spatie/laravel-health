<?php

namespace Spatie\Health\Checks;

use Cron\CronExpression;
use Illuminate\Console\Scheduling\ManagesFrequencies;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use Spatie\Health\Enums\Status;

abstract class Check
{
    use ManagesFrequencies;

    protected string $expression = '* * * * *';
    protected ?string $name = null;
    protected ?string $label = null;

    /**
     * @var string[]
     */
    protected array $groups = [];

    final public function __construct()
    {
    }

    public static function new(): static
    {
        $instance = new static();

        $instance->everyMinute();

        return $instance;
    }

    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): string
    {
        if ($this->label) {
            return $this->label;
        }

        $name = $this->getName();

        return Str::of($name)->snake()->replace('_', ' ')->title();
    }

    public function getName(): string
    {
        if ($this->name) {
            return $this->name;
        }

        $baseName = class_basename(static::class);

        return Str::of($baseName)->beforeLast('Check');
    }

    /**
     * @param  string|string[]  $groups
     *
     * @return $this
     */
    public function groups(string|array $groups): self
    {
        $this->groups = Arr::wrap($groups);

        return $this;
    }

    /**
     * @return string[]
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * @param  string|null  $group
     *
     * @return bool
     */
    public function shouldRun(?string $group = null): bool
    {
        $date = Date::now();

        $cron = (new CronExpression($this->expression))->isDue($date->toDateTimeString());

        return $group === null ?
          $cron :
          $cron && in_array($group, $this->groups, true);
    }

    abstract public function run(): Result;

    public function markAsCrashed(): Result
    {
        return new Result(Status::crashed());
    }

    public function onTerminate(mixed $request, mixed $response): void
    {
    }
}
