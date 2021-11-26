<?php

namespace Spatie\Health\Commands;

use Illuminate\Console\Command;
use Spatie\Health\Checks\Checks\EnvironmentCheck;
use Spatie\Health\Checks\Checks\HorizonCheck;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;
use Spatie\Health\Facades\Health;
use Spatie\Health\ResultStores\ResultStore;
use function Termwind\render;

class ListChecksCommand extends Command
{
    public $signature = 'health:list';

    public $description = 'List all health checks';

    public function handle(): int
    {
        $resultStore = app(ResultStore::class);

        $checkResults = $resultStore->latestResults();

        $checks = Health::registeredChecks();

        render(view('health::cli.list', compact('checks')));

        return self::SUCCESS;
    }
}
