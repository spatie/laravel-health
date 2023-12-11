<?php

namespace Spatie\Health\Testing;

use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

class FakeCheck extends Check
{
    private Check $fakedCheck;

    private FakeValues $fakeValues;

    public static function result(Result $result, ?bool $shouldRun = null): FakeValues
    {
        return new FakeValues($result, $shouldRun);
    }

    public function fake(Check $fakedCheck, FakeValues $values): self
    {
        $this->fakedCheck = $fakedCheck;
        $this->fakeValues = $values;

        $this->name($fakedCheck->getName());
        $this->label($fakedCheck->getLabel());

        return $this;
    }

    public function shouldRun(): bool
    {
        return $this->fakeValues->shouldRun() === null
            ? $this->fakedCheck->shouldRun()
            : $this->fakeValues->shouldRun();
    }

    public function onTerminate(mixed $request, mixed $response): void
    {
        $this->fakedCheck->onTerminate($request, $response);
    }

    public function run(): Result
    {
        return $this->fakeValues->getResult();
    }
}
