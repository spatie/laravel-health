<?php

namespace Spatie\Health\Commands;

use Illuminate\Console\Command;

class HealthCommand extends Command
{
    public $signature = 'laravel-health';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
