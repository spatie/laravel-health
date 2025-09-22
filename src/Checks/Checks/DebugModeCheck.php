<?php

namespace Spatie\Health\Checks\Checks;

use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

use function config;
use function __;

class DebugModeCheck extends Check
{
    protected bool $expected = false;

    public function __construct()
    {
        parent::__construct();
        $this->label(__('health::checks.titles.debug_mode'));
    }

    public function expectedToBe(bool $bool): self
    {
        $this->expected = $bool;

        return $this;
    }

    public function run(): Result
    {
        $actual = config('app.debug');

        $result = Result::make()
            ->meta([
                'actual' => $actual,
                'expected' => $this->expected,
            ])
            ->shortSummary($this->convertToWord($actual));

        return $this->expected === $actual
            ? $result->ok()
            : $result->failed(__('health::checks.debug_mode.expected_but_was', [
                'expected' => $this->convertToWord((bool) $this->expected),
                'actual' => $this->convertToWord((bool) $actual),
            ]));
    }

    protected function convertToWord(bool $boolean): string
    {
        return $boolean ? 'true' : 'false';
    }
}
