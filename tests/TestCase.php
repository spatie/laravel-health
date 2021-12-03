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

    protected function getPackageProviders($app)
    {
        return [
            HealthServiceProvider::class,
            HorizonServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        Schema::dropAllTables();

        $migration = include __DIR__.'/../database/migrations/create_health_tables.php.stub';
        $migration->up();
    }

    protected function fakeHorizonStatus(string $status)
    {
        $masters = Mockery::mock(MasterSupervisorRepository::class);
        $masters->shouldReceive('all')->andReturn([
            (object) [
                'status' => $status,
            ],
            (object) [
                'status' => $status,
            ],
        ]);

        $this->app->instance(MasterSupervisorRepository::class, $masters);
    }

    public function refreshServiceProvider(): void
    {
        (new HealthServiceProvider($this->app))->packageBooted();
    }
}
