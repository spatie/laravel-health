<?php

namespace Spatie\Health\Commands;

use Illuminate\Console\Command;

class ListChecksCommand extends Command
{
    public $signature = 'health:list';

    public $description = 'List all health checks';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
