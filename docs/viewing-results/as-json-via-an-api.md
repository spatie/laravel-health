---
title: As JSON via an API
weight: 3
---

The package contains a controller`Spatie\Health\Http\Controllers\HealthCheckResultsController` that can be used the render the latest results of all checks as JSON.  Simply use that controller in your routes on any URL you desire.

```php
use Spatie\Health\Http\Controllers\HealthCheckResultsController

Route::get('health', HealthCheckResultsController::class);
```

This controller will respond with JSON to any request that accepts JSON.

If you don't want these results to be public, be sure to take care of authorization yourself.
