<?php

namespace Spatie\Health\Commands;

use Illuminate\Console\Command;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Checks\HeartbeatCheck;
use Spatie\Health\Facades\Health;

abstract class HeartbeatCommand extends Command
{
    public function handle(): int
    {
        $check = $this->getCheckInstance();

        if (! $check) {
            $this->error("In order to use this command, you should register the `{$this->getCheckClass()}`");

            return static::FAILURE;
        }

        if (! $check instanceof HeartbeatCheck) {
            $this->error("Class {$this->getCheckClass()} must be instance of `Spatie\Health\Checks\Checks\HeartbeatCheck`");

            return static::FAILURE;
        }

        $cacheKey = $check->getCacheKey();

        if (! $cacheKey) {
            $this->error("You must set the `cacheKey` of `{$this->getCheckClass()}` to a non-empty value");

            return static::FAILURE;
        }

        return $this->runHeartbeat();
    }

    public function getCheckInstance(): null|HeartbeatCheck
    {
        $class = $this->getCheckClass();

        return Health::registeredChecks()->first(
            fn (Check $check) => $check instanceof $class
        );
    }

    public abstract function runHeartbeat(): int;

    public abstract function getCheckClass(): string;
}
