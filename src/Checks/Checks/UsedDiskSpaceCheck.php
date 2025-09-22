<?php

namespace Spatie\Health\Checks\Checks;

use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;
use Spatie\Regex\Regex;
use Symfony\Component\Process\Process;

use function __;

class UsedDiskSpaceCheck extends Check
{
    protected int $warningThreshold = 70;

    protected int $errorThreshold = 90;

    protected ?string $filesystemName = null;

    public function __construct()
    {
        parent::__construct();
        $this->label(__('health::checks.titles.used_disk_space'));
    }

    public function filesystemName(string $filesystemName): self
    {
        $this->filesystemName = $filesystemName;

        return $this;
    }

    public function warnWhenUsedSpaceIsAbovePercentage(int $percentage): self
    {
        $this->warningThreshold = $percentage;

        return $this;
    }

    public function failWhenUsedSpaceIsAbovePercentage(int $percentage): self
    {
        $this->errorThreshold = $percentage;

        return $this;
    }

    public function run(): Result
    {
        $diskSpaceUsedPercentage = $this->getDiskUsagePercentage();

        $result = Result::make()
            ->meta(['disk_space_used_percentage' => $diskSpaceUsedPercentage])
            ->shortSummary($diskSpaceUsedPercentage.'%');

        if ($diskSpaceUsedPercentage > $this->errorThreshold) {
            return $result->failed(__('health::checks.used_disk_space.almost_full_error', [
                'percentage' => $diskSpaceUsedPercentage,
            ]));
        }

        if ($diskSpaceUsedPercentage > $this->warningThreshold) {
            return $result->warning(__('health::checks.used_disk_space.almost_full_warning', [
                'percentage' => $diskSpaceUsedPercentage,
            ]));
        }

        return $result->ok();
    }

    protected function getDiskUsagePercentage(): int
    {
        $process = Process::fromShellCommandline('df -P '.($this->filesystemName ?: '.'));

        $process->run();

        $output = $process->getOutput();

        return (int) Regex::match('/(\d*)%/', $output)->group(1);
    }
}
