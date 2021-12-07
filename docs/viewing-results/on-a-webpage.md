---
title: On a webpage
weight: 1
---

The package contains a controller`Spatie\Health\Http\Controllers\HealthCheckResultsController` that can be used the render the latest results of all checks.  Simply use that controller in your routes on any URL you desire. 

```php
use Spatie\Health\Http\Controllers\HealthCheckResultsController

Route::get('health', HealthCheckResultsController::class);
```

If you don't want these results to be public, be sure to take care of authorization yourself.

## Running the checks before rendering the page

If you want to run the checks in the same request, you can pass the `fresh` query parameter.

```
https://example.com/health?fresh
```

This way you'll see the fresh results in the browser.
