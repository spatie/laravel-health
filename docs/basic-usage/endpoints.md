---
title: Endpoints
weight: 5
---

The package offers a couple of controllers that can be used to check the health of your application.


## A beautiful status page

The `HealthCheckResultsController` will display a beautiful page with all health check results. You can find more detailed information on this page [here](/docs/laravel-health/v1/viewing-results/on-a-webpage).

## Simple health check

The `SimpleHealthCheckController` will return either a status of `200` for a healthy application
or `503` for a unhealthy one without exposing any sensitive information about your application.

This is particularly helpful when you want to check the readiness of a container or a pod as they infer this by the responses status code.

```php
Route::get('health', \Spatie\Health\Http\Controllers\SimpleHealthCheckController::class);
```

## Detailed health check

Alternatively you can also register the `HealthCheckJsonResultsController`, this one will give you a detailed view of all
the checks that have been run with their status and meta data. This endpoint will also always return a status of `200` unless 
something really goes wrong. 

If you don't want to expose this info, you can add an `auth` middleware.

```php
Route::middleware('auth')->get('health', \Spatie\Health\Http\Controllers\HealthCheckJsonResultsController::class);
```
