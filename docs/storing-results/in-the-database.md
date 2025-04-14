---
title: In the database
weight: 3
---

When using the `Spatie\Health\ResultStores\EloquentHealthResultStore` result store the latest check results will be written to the database. The advantage of stores results in the database, is that you can build up the history of your results. The downside is that, if your database is down, no results can be written.

In the `health` config file, the store can be configured in the `health_stores` key like this:

```php
return [
    'result_stores' => [
        EloquentHealthResultStore::class,
    ],
```

The results will be written in the `health_check_result_history_items` table. All field names should be self-explanatory. Using the `App\Spatie\Models\HealthCheckResultHistoryItem` model, you can easily query all results.

## Using a custom model

If you'd like to use a custom model for storing health check results, extend the `Spatie\Health\Models\HealthCheckResultHistoryItem` class, and add it to the `EloquentHealthResultStore` configuration:

```php
return [
    'result_stores' => [
        Spatie\Health\ResultStores\EloquentHealthResultStore::class => [
            'model' => App\Models\CustomHealthCheckResultModel::class,
        ],
    ],
```

## Pruning the results table

The model uses the [Laravel's `MassPrunable` trait](https://laravel.com/docs/12.x/eloquent#pruning-models). In the `health` config file, you can specify the maximum age of a model in the `keep_history_for_days` key. Don't forget to schedule the `model:prune` command, as instructed in Laravel's docs. You'll have to explicitly add the model class when it is not in your `App\` namespace:

```php
// in routes/console.php
use Illuminate\Support\Facades\Schedule;

Schedule::command('model:prune', ['--model' => [\Spatie\Health\Models\HealthCheckResultHistoryItem::class]])->daily();
```
