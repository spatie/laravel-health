<?php

use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\Facades\Health;
use Spatie\Health\Tests\TestClasses\FakeUsedDiskSpaceCheck;

use function Pest\Laravel\artisan;
use function Pest\Laravel\get;
use function Spatie\PestPluginTestTime\testTime;
use function Spatie\Snapshots\assertMatchesSnapshot;

beforeEach(function () {
    testTime()->freeze('2021-01-01 00:00:00');

    $this->check = FakeUsedDiskSpaceCheck::new()->fakeDiskUsagePercentage(100);

    Health::checks([
        $this->check,
    ]);

    config()->set('health.oh_dear_endpoint', [
        'enabled' => true,
        'secret' => 'my-secret',
        'url' => 'my-url',
    ]);

    $this->refreshServiceProvider();
});

it('will display the results as json when the endpoint is enabled and the secret is correct', function () {
    artisan(RunHealthChecksCommand::class);

    $json = get('my-url', ['oh-dear-health-check-secret' => 'my-secret'])
        ->assertSuccessful()
        ->json();

    assertMatchesSnapshot($json);
});

it('will not display the results when the endpoint is disabled', function () {
    artisan(RunHealthChecksCommand::class);

    config()->set('health.oh_dear_endpoint', [
        'enabled' => false,
        'secret' => 'my-secret',
        'url' => 'my-other-url',
    ]);

    $this->refreshServiceProvider();

    get('my-other-url', ['oh-dear-health-check-secret' => 'my-secret'])->assertStatus(404);
});

it('will run the checks when visiting the endpoint if the relevant config option is set to true', function () {
    config()->set('health.oh_dear_endpoint.always_send_fresh_results', true);

    $json = get('my-url', ['oh-dear-health-check-secret' => 'my-secret'])
        ->assertSuccessful()
        ->json();

    assertMatchesSnapshot($json);
});

it('will not display the results when the secret is wrong', function () {
    get('my-url', ['oh-dear-health-check-secret' => 'wrong-secret'])->assertStatus(403);
});

it('will not display the results when the secret is missing', function () {
    get('my-url')->assertStatus(403);
});
