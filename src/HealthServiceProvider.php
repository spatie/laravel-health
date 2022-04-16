<?php

namespace Spatie\Health;

use Illuminate\Support\Facades\Route;
use Spatie\Health\Commands\ListHealthChecksCommand;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\Commands\ScheduleCheckHeartbeatCommand;
use Spatie\Health\Components\Logo;
use Spatie\Health\Components\StatusIndicator;
use Spatie\Health\Http\Controllers\HealthCheckJsonResultsController;
use Spatie\Health\Http\Controllers\SimpleHealthCheckController;
use Spatie\Health\Http\Middleware\RequiresSecret;
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
            ->hasViewComponents('health', Logo::class)
            ->hasViewComponents('health', StatusIndicator::class)
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

    public function packageBooted(): void
    {
        $this->app->make(Health::class)->inlineStylesheet(file_get_contents(__DIR__.'/../resources/dist/health.min.css'));

        $this->registerOhDearEndpoint();
        $this->registerSimpleHealthCheckEndpoint();
    }

    protected function registerOhDearEndpoint(): self
    {
        if (! config('health.oh_dear_endpoint.enabled')) {
            return $this;
        }

        if (! config('health.oh_dear_endpoint.secret')) {
            return $this;
        }

        if (! config('health.oh_dear_endpoint.url')) {
            return $this;
        }

        Route::get(config('health.oh_dear_endpoint.url'), HealthCheckJsonResultsController::class)
            ->middleware(RequiresSecret::class);

        return $this;
    }

    protected function registerSimpleHealthCheckEndpoint(): self
    {
        if (! config('health.simple_health_check_endpoint.enabled')) {
            return $this;
        }

        if (! config('health.oh_dear_endpoint.url')) {
            return $this;
        }

        Route::get(config('health.simple_health_check_endpoint.url'), SimpleHealthCheckController::class);

        return $this;
    }
}
