<?php

namespace Spatie\Health\Tests\Http\Controllers;

use Orchestra\Testbench\TestCase;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Enums\Status;
use Spatie\Health\Facades\Health;
use Spatie\Health\HealthServiceProvider;
use Spatie\Health\ResultStores\ResultStore;
use Spatie\Health\ResultStores\StoredCheckResults\StoredCheckResult;
use Spatie\Health\Tests\TestClasses\InMemoryResultStore;

class NagiosHealthCheckResultsControllerTest extends TestCase
{
    private ResultStore $resultStore;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resultStore = new InMemoryResultStore();
        $this->app->instance(ResultStore::class, $this->resultStore);

        config()->set('health.nagios_endpoint', [
            'enabled'      => true,
            'bearer_token' => 'my-bearer-token',
            'url'          => '/health/nagios',
        ]);
        (new HealthServiceProvider($this->app))->packageBooted();
    }

    protected function getPackageProviders($app): array
    {
        return [
            HealthServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Health' => Health::class,
        ];
    }

    /** @test */
    public function it_returns_returns_404_when_disabled()
    {
        config()->set('health.nagios_endpoint', [
            'enabled'      => false,
            'bearer_token' => 'my-bearer-token',
            'url'          => '/health/other-nagios-url',
        ]);
        (new HealthServiceProvider($this->app))->packageBooted();

        $response = $this->withToken('my-bearer-token')->get('/health/other-nagios-url');

        $response->assertStatus(404);
    }

    /** @test */
    public function it_returns_returns_unauthorised_without_bearer_token()
    {
        $response = $this->get('/health/nagios');

        $response->assertStatus(403);
    }

    /** @test */
    public function it_returns_nagios_formatted_results()
    {
        Health::checks([
            DatabaseCheck::new(),
        ]);

        $checkResults = collect([
            new StoredCheckResult(
                name: 'Database',
                label: 'Database',
                notificationMessage: '',
                shortSummary: 'Connection successful',
                status: Status::ok(),
                meta: ['response_time' => '100ms']
            ),
            new StoredCheckResult(
                name: 'DiskSpace',
                label: 'Disk Space',
                notificationMessage: '',
                shortSummary: 'Usage at 85%',
                status: Status::ok(),
                meta: ['used_space' => '85%']
            )
        ]);

        $this->resultStore->save($checkResults);

        $response = $this->withToken('my-bearer-token')->get('/health/nagios');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $this->assertStringContainsString('OK: 2 checks executed|' . PHP_EOL, $response->getContent());
        $this->assertStringContainsString('Database:  [OK]', $response->getContent());
        $this->assertStringContainsString('Disk Space:  [OK]', $response->getContent());
    }

    /** @test */
    public function it_returns_error_status_when_checks_fail()
    {
        $checkResults = collect([
            new StoredCheckResult(
                name: 'DiskSpace',
                label: 'Disk Space',
                notificationMessage: '',
                shortSummary: 'Usage at 85%',
                status: Status::failed(),
                meta: ['used_space' => '85%']
            )
        ]);

        $this->resultStore->save($checkResults);

        $response = $this->withToken('my-bearer-token')->get('health/nagios');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $this->assertStringContainsString('CRITICAL:', $response->getContent());
        $this->assertStringContainsString('Disk Space:  [FAILED]', $response->getContent());
    }

    /** @test */
    public function it_handles_no_results()
    {
        $this->resultStore->flush();

        $response = $this->withToken('my-bearer-token')->get('health/nagios');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $this->assertStringContainsString('UNKNOWN', $response->getContent());
    }
}
