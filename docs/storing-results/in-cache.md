---
title: In the cache
weight: 2
---

When using the `Spatie\Health\ResultStores\CacheHealthResultStore` result store the latest check results will be written to the cache.

In the `health` config file, the store can be configured in the `health_stores` key like this:

```php
return [
    'result_stores' => [
        CacheHealthResultStore::class => [
            'store' => 'file',
        ],
    ],
```

The `store` key can be set to any of the stores you configured in `config/cache.php`.
