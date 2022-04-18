---
title: Endpoints
weight: 5
---

You can also register endpoints to check the health of your application.

## Simple Health Check

The `SimpleHealthCheckController` for example can be registered and return either a status of `200` for a healthy application
or `503` for a unhealthy one without exposing any sensitive information about your application.
This is particularly helpful when you want to check the readiness of a container or a pod as they infer this by the responses status code 

```php
Route::get('/health', \Spatie\Health\Http\Controllers\SimpleHealthCheckController::class);
```

## Detailed Health Check

Alternatively you can also register the `HealthCheckJsonResultsController`, this one will give you a detailed view of all
the checks that have been run with their status and meta data. This endpoint will also always return a status of `200` unless 
something really goes wrong. As this endpoint exposes information about your application that you might not want everyone
to know you should best put it behind a guard.

```php
Route::middleware(['auth'])->get('/health', \Spatie\Health\Http\Controllers\HealthCheckJsonResultsController::class);
```
or like this
```php
Route::middleware(['my-custom-auth-guard'])->get('/health', \Spatie\Health\Http\Controllers\HealthCheckJsonResultsController::class);
```

## A webpage

Yet another alternative is the `HealthCheckResultsController`, this one does not give you an api ready response but a website
that you can see all you checks and their status on. You can find more detailed information on this page [here](/docs/viewing-results/on-a-webpage).
