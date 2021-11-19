<?php

namespace Spatie\Health;

use Spatie\Health\Commands\ListChecksCommand;
use Spatie\Health\Commands\RunChecksCommand;
use Spatie\Health\ResultStores\ResultStore;
use Spatie\Health\ResultStores\ResultStores;
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
        $this->app->bind('health', Health::class);

        $this->app->bind(ResultStore::class, fn () => ResultStores::createFromConfig()->first());
    }
}
