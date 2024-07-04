<?php

namespace Spatie\Health\Checks;

use Cron\CronExpression;
use Illuminate\Console\Scheduling\ManagesFrequencies;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Laravel\SerializableClosure\SerializableClosure;
use Spatie\Health\Enums\Status;

abstract class Check
{
    use Conditionable {
        unless as doUnless;
    }
    use Macroable;
    use ManagesFrequencies;

    protected string $expression = '* * * * *';

    protected ?string $name = null;

    protected ?string $label = null;

    /**
     * @var array<bool|callable(): bool>
     */
    protected array $shouldRun = [];

    public function __construct() {}

    public static function new(): static
    {
        $instance = app(static::class);

        $instance->everyMinute();

        return $instance;
    }

    public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function label(string $label): static
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

    public function getRunConditions(): array
    {
        return $this->shouldRun;
    }

    public function shouldRun(): bool
    {
        foreach ($this->shouldRun as $shouldRun) {
            $shouldRun = is_callable($shouldRun) ? $shouldRun() : $shouldRun;

            if (! $shouldRun) {
                return false;
            }
        }

        $date = Date::now();

        return (new CronExpression($this->expression))->isDue($date->toDateTimeString());
    }

    public function if(bool|callable $condition)
    {
        $this->shouldRun[] = $condition;

        return $this;
    }

    public function unless(bool|callable $condition)
    {
        $this->shouldRun[] = is_callable($condition) ?
            fn () => ! $condition() :
            ! $condition;

        return $this;
    }

    abstract public function run(): Result;

    public function markAsCrashed(): Result
    {
        return new Result(Status::crashed());
    }

    public function onTerminate(mixed $request, mixed $response): void {}

    public function __serialize(): array
    {
        $vars = get_object_vars($this);

        $serializedShouldRun = [];
        foreach ($vars['shouldRun'] as $shouldRun) {
            if ($shouldRun instanceof \Closure) {
                $serializedShouldRun[] = new SerializableClosure($shouldRun);
            } else {
                $serializedShouldRun[] = $shouldRun;
            }
        }

        $vars['shouldRun'] = $serializedShouldRun;

        return $vars;
    }

    public function __unserialize(array $data): void
    {
        foreach ($data as $property => $value) {
            $this->$property = $value;
        }

        $deserializedShouldRun = [];

        foreach ($this->shouldRun as $shouldRun) {
            if ($shouldRun instanceof SerializableClosure) {
                $deserializedShouldRun[] = $shouldRun->getClosure();
            } else {
                $deserializedShouldRun[] = $shouldRun;
            }
        }

        $this->shouldRun = $deserializedShouldRun;
    }
}
