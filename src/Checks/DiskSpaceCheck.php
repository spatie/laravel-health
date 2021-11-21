<?php

namespace Spatie\Health\Checks;

use Spatie\Health\Support\Result;
use Spatie\Regex\Regex;
use Symfony\Component\Process\Process;

class DiskSpaceCheck extends Check
{
    protected int $warningThreshold = 70;
    protected int $errorThreshold = 90;

    public function warnWhenUsedSpaceIsAbovePercentage(int $percentage): self
    {
        $this->warningThreshold = $percentage;

        return $this;
    }

    public function errorWhenUsedSpaceIsAbovePercentage(int $percentage): self
    {
        $this->errorThreshold = $percentage;

        return $this;
    }

    public function run(): Result
    {
        $spaceUsedPercentage = $this->getDiskUsagePercentage();

        $result = Result::make()->meta(['disk_space_used_percentage' => $spaceUsedPercentage]);

        if ($spaceUsedPercentage > $this->errorThreshold) {
            return $result->failed("The disk is almost full: (:disk_space_used_percentage % used)");
        }

        if ($spaceUsedPercentage > $this->warningThreshold) {
            return $result->warning("The disk is almost full: (:disk_space_used_percentage % used)");
        }

        return $result->ok();
    }

    protected function getDiskUsagePercentage(): int
    {
        $process = Process::fromShellCommandline('df -P .');

        $process->run();

        $output = $process->getOutput();

        return (int) Regex::match('/(\d?\d)%/', $output)->group(1);
    }
}
