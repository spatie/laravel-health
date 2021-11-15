<?php

namespace Spatie\Health\Commands;

use Illuminate\Console\Command;

class RunChecksCommand extends Command
{
    public $signature = 'health:run-checks';

    public $description = 'Run all health checks';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
