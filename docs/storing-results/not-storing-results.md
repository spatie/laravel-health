---
title: Not storing results
weight: 4
---

If you only want to trigger the results [by visiting the HTTP or JSON endpoints](
/docs/laravel-health/v1/viewing-results/on-a-webpage#running-the-checks-before-rendering-the-page), you may opt to not store any results at all.

In the `health` config file, you should configure `health_stores` key like this:

```php
return [
    'result_stores' => [
        Spatie\Health\ResultStores\InMemoryHealthResultStore::class,
    ],
```
