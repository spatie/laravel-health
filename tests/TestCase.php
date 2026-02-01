<?php

namespace Spatie\Health\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Schema;
use Laravel\Horizon\Contracts\MasterSupervisorRepository;
use Laravel\Horizon\HorizonServiceProvider;
use Mockery;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Health\HealthServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Spatie\\Health\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app): array
    {
        return [
            HealthServiceProvider::class,
            HorizonServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        Schema::dropAllTables();

        $migration = include __DIR__.'/../database/migrations/create_health_tables.php.stub';
        $migration->up();
    }

    protected function fakeHorizonStatus(string $status): void
    {
        $masters = Mockery::mock(MasterSupervisorRepository::class);
        $masters->shouldReceive('all')->andReturn(
            $status === 'down' ? [] : [
                (object) ['status' => $status],
                (object) ['status' => $status],
            ]
        );

        $this->app->instance(MasterSupervisorRepository::class, $masters);
    }

    protected function fakeHorizonStatusSequence(array $statuses): void
    {
        $masters = Mockery::mock(MasterSupervisorRepository::class);

        $masters->shouldReceive('all')
            ->andReturnUsing(function () use (&$statuses) {
                $status = array_shift($statuses);

                return $status === 'down' ? [] : [
                    (object) ['status' => $status],
                    (object) ['status' => $status],
                ];
            });

        $this->app->instance(MasterSupervisorRepository::class, $masters);
    }

    public function refreshServiceProvider(): void
    {
        (new HealthServiceProvider($this->app))->packageBooted();
    }
}
