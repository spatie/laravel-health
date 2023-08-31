<?php

namespace Spatie\Health\Checks\Checks;

use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

use function config;

class DebugModeCheck extends Check
{
    protected bool $expected = false;

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
            : $result->failed("The debug mode was expected to be `{$this->convertToWord((bool) $this->expected)}`, but actually was `{$this->convertToWord((bool) $actual)}`");
    }

    protected function convertToWord(bool $boolean): string
    {
        return $boolean ? 'true' : 'false';
    }
}
