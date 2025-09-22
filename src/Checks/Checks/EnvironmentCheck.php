<?php

namespace Spatie\Health\Checks\Checks;

use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

use function app;
use function __;

class EnvironmentCheck extends Check
{
    protected string $expectedEnvironment = 'production';

    public function __construct()
    {
        parent::__construct();
        $this->label(__('health::checks.titles.environment'));
    }

    public function expectEnvironment(string $expectedEnvironment): self
    {
        $this->expectedEnvironment = $expectedEnvironment;

        return $this;
    }

    public function run(): Result
    {
        $actualEnvironment = (string) app()->environment();

        $result = Result::make()
            ->meta([
                'actual' => $actualEnvironment,
                'expected' => $this->expectedEnvironment,
            ])
            ->shortSummary($actualEnvironment);

        return $this->expectedEnvironment === $actualEnvironment
            ? $result->ok()
            : $result->failed(__('health::checks.environment.expected_but_was', [
                'expected' => $this->expectedEnvironment,
                'actual' => $actualEnvironment,
            ]));
    }
}
