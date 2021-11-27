<?php

namespace Spatie\Health;

use Spatie\Health\Commands\ListHealthChecksCommand;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\Commands\ScheduleCheckHeartbeatCommand;
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
            ->hasTranslations()
            ->hasMigration('create_health_tables')
            ->hasCommands(
                ListHealthChecksCommand::class,
                RunHealthChecksCommand::class,
                ScheduleCheckHeartbeatCommand::class,
            );
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(Health::class, fn () => new Health());
        $this->app->bind('health', Health::class);

        $this->app->bind(ResultStore::class, fn () => ResultStores::createFromConfig()->first());
    }
}
