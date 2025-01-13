<?php

namespace Spatie\Health;

use Illuminate\Support\Facades\Route;
use Spatie\Health\Commands\DispatchQueueCheckJobsCommand;
use Spatie\Health\Commands\ListHealthChecksCommand;
use Spatie\Health\Commands\PauseHealthChecksCommand;
use Spatie\Health\Commands\ResumeHealthChecksCommand;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\Commands\ScheduleCheckHeartbeatCommand;
use Spatie\Health\Components\Logo;
use Spatie\Health\Components\StatusIndicator;
use Spatie\Health\Http\Controllers\HealthCheckJsonResultsController;
use Spatie\Health\Http\Middleware\RequiresSecret;
use Spatie\Health\Jobs\HealthQueueJob;
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
                DispatchQueueCheckJobsCommand::class,
                PauseHealthChecksCommand::class,
                ResumeHealthChecksCommand::class,
            );
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(Health::class);
        $this->app->alias(Health::class, 'health');

        $this->app->bind(ResultStore::class, fn () => ResultStores::createFromConfig()->first());
    }

    public function packageBooted(): void
    {
        $this->app->make(Health::class)->inlineStylesheet(file_get_contents(__DIR__.'/../resources/dist/health.min.css'));

        $this
            ->registerOhDearEndpoint()
            ->silenceHealthQueueJob();
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

    protected function silenceHealthQueueJob(): self
    {
        if (! config('health.silence_health_queue_job', true)) {
            return $this;
        }

        $silencedJobs = config('horizon.silenced', []);

        if (in_array(HealthQueueJob::class, $silencedJobs)) {
            return $this;
        }

        $silencedJobs[] = HealthQueueJob::class;

        config()->set('horizon.silenced', $silencedJobs);

        return $this;
    }
}
