---
title: Not storing results
weight: 4
---

If you only want to trigger the results by visiting the HTTP or JSON endpoints, you may opt to not store any results at all.

In the `health` config file, you should configure `health_stores` key like this:

```php
return [
    'result_stores' => [
        Spatie\Health\ResultStores\InMemoryHealthResultStore::class,
    ],
```
