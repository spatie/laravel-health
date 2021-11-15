<?php

namespace Spatie\Health;

use Spatie\Health\Checks\DiskspaceCheck;
use Spatie\Health\Commands\ListChecksCommand;
use Spatie\Health\Commands\RunChecksCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class HealthServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-health')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_health_tables')
            ->hasCommands(
                ListChecksCommand::class,
                RunChecksCommand::class,
            );
    }

    public function packageBooted()
    {
        $this->app->singleton(Health::class, fn () => new Health());
    }

    public function checks()
    {
        Health::registerChecks([
            DiskspaceCheck::new()
                ->warnWhenFreeSpaceIsBelowPercentage(20)
                ->errorWhenFreeSpaceIsBelowPercentage(10),
        ]);

        return [
            DiskspaceCheck::new()
                ->warnWhenFreeSpaceIsBelowPercentage(20)
                ->errorWhenFreeSpaceIsBelowPercentage(10),
        ];
    }
}
