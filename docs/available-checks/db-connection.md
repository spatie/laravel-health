---
title: DB connection
weight: 6
---

This check makes sure your application can connect to a database. If the `default` database connection does not work, this check will fail.

## Usage

Here's how you can register the check.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\DatabaseCheck;

Health::checks([
    DatabaseCheck::new(),
]);
```


### Specifying the database connection

To check another database connection, call `connectionName()`

```php
DatabaseCheck::new()->connectionName('another-connection-name'),
```
