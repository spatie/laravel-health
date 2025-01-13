<?php

namespace Spatie\Health\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ResumeHealthChecksCommand extends Command
{
    protected $signature = 'health:resume';

    protected $description = 'Resume all health checks';

    public function handle(): int
    {
        Cache::forget(PauseHealthChecksCommand::CACHE_KEY);

        $this->comment('All health check resumed');

        return self::SUCCESS;
    }
}
