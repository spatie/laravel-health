<?php

namespace Spatie\Health\Tests\TestClasses;

use Psr\Log\LoggerInterface;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;
use Spatie\Health\Enums\Status;

class DependencyInjectionCheck extends Check
{
    protected LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;

        parent::__construct();
    }

    public function run(): Result
    {
        $this->logger->info('Dependency injection worked');

        return new Result(
            Status::ok(),
        );
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }
}
