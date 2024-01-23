---
title: Redis
weight: 17
---

This check will make sure Redis is running. By default, this check will make sure the `default` connection is working.

## Usage

Here's how you can register the check.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\RedisCheck;

Health::checks([
    RedisCheck::new(),
]);
```


### Customizing the thresholds

To customize the monitored Redis connection name, call `connectionName`.

```php
RedisCheck::new()->connectionName('other-redis-connection-name'),
```
