---
title: DB table size
weight: 6
---

This check makes sure your the tables of your database are not too big. This check support MySQL and Postgres.

If one of the given tables is bigger than the specific maximum, the check will fail.

## Usage

Here's how you can register the check.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\DatabaseTableSizeCheck;

Health::checks([
    DatabaseTableSizeCheck::new()
        ->table('your_table_name', maxSizeInMb: 1_000),
        ->table('another_table_name', maxSizeInMb: 2_000),
]);
```

### Specifying the database connection

To check another database connection, call `connectionName()`

```php
DatabaseCheck::new()->connectionName('another-connection-name'),
```
