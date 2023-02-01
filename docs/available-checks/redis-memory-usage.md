---
title: Redis memory usage
weight: 17
---

This check makes sure that Redis is not consuming too much memory.

If the memory usage is larger than the specified maximum, this check will fail.

## Usage

Here's how you can register the check.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\RedisMemoryUsageCheck;

Health::checks([
    RedisMemoryUsageCheck::new()->failWhenAboveMb(1000),
]);
```

### Using different connection

To customize the monitored Redis connection name, call `connectionName`.

```php
RedisMemoryUsageCheck::new()->connectionName('other-redis-connection-name'),
```
