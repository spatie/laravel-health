<?php

namespace Spatie\Health\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class PauseHealthChecksCommand extends Command
{
    public const CACHE_KEY = 'health_paused';

    public const DEFAULT_TTL = 300;

    protected $signature = 'health:pause {seconds='.self::DEFAULT_TTL.'}';

    protected $description = 'Pause all health checks for the giving time';

    public function handle(): int
    {
        $seconds = (int) $this->argument('seconds');

        Cache::put(self::CACHE_KEY, true, $seconds);

        $this->comment('All health check paused until '.now()->addSeconds($seconds)->toDateTimeString());

        return self::SUCCESS;
    }
}
