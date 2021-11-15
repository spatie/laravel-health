<?php

namespace Spatie\Health;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\Health\Commands\HealthCommand;

class HealthServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-health')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-health_table')
            ->hasCommand(HealthCommand::class);
    }
}
