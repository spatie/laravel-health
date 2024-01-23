---
title: Application Cache
weight: 3
---

This check makes sure the application can connect to your cache system and read/write to the cache keys. By default, this check will make sure the `default` connection is working.

## Usage

Here's how you can register the check.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\CacheCheck;

Health::checks([
    CacheCheck::new(),
]);
```


### Specifying the cache driver

To check another cache driver, call `driver()`.

```php
CacheCheck::new()->driver('another-cache-driver'),
```
