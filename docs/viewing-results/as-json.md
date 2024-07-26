---
title: As JSON
weight: 4
---

The package contains a controller `Spatie\Health\Http\Controllers\HealthCheckJsonResultsController` that can be used the render the latest results of all checks as JSON.  Simply use that controller in your routes on any URL you desire.

```php
use Spatie\Health\Http\Controllers\HealthCheckJsonResultsController

Route::get('health', HealthCheckJsonResultsController::class);
```

This controller will respond with JSON to any request that accepts JSON.

If you don't want these results to be public, be sure to take care of authorization yourself.

## Running the checks

If you want to run the checks in the same request, you can pass the `fresh` query parameter.

```
https://example.com/health?fresh
```

This way you'll see the latest results in the JSON.

## Status codes

By default, a 200 response will be returned regardless of the results. You can change the status code that
will be returned if a check fails using the `health.json_results_failure_status` config value.