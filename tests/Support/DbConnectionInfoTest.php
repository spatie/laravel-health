<?php

use Illuminate\Database\ConnectionResolverInterface;
use Spatie\Health\Models\HealthCheckResultHistoryItem;
use Spatie\Health\ResultStores\EloquentHealthResultStore;
use Spatie\Health\Support\DbConnectionInfo;
use Spatie\Health\Tests\TestClasses\CrashingHealthCheckResultHistoryItem;

it('can determine the table size in mb', function () {
    $connection = app(ConnectionResolverInterface::class)->connection('mysql');

    $connectionInfo = new DbConnectionInfo;

    $size = $connectionInfo->tableSizeInMb($connection, 'health_check_result_history_items');

    expect($size)->toBeGreaterThan(0);
});

it('can determine the connection count', function () {
    $connection = app(ConnectionResolverInterface::class)->connection('mysql');

    $connectionInfo = new DbConnectionInfo;

    $size = $connectionInfo->connectionCount($connection);

    expect($size)->toBeGreaterThan(0);
});

it('correctly determines the connection of the model', function () {
    $model = new CrashingHealthCheckResultHistoryItem;

    expect($model->getConnectionName())->toBe('custom');

    $model = new HealthCheckResultHistoryItem;

    expect($model->getConnectionName())->toBe(config('database.default'));

    config()->set('health.result_stores', [
        EloquentHealthResultStore::class => [
            'connection' => 'custom_in_config',
        ],
    ]);

    expect($model->getConnectionName())->toBe(config('health.result_stores.'.EloquentHealthResultStore::class.'.connection'));
});
