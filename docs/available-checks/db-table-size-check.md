---
title: DB table size
weight: 9
---

This check makes sure the tables of your database are not too big. This check supports MySQL and Postgres.

If one of the given tables is bigger than the specific maximum, the check will fail.

## Requirements

You'll need to install the `doctrine/dbal` package in your project.

```bash
composer require doctrine/dbal
```


## Usage

Here's how you can register the check.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\DatabaseTableSizeCheck;

Health::checks([
    DatabaseTableSizeCheck::new()
        ->table('your_table_name', maxSizeInMb: 1_000)
        ->table('another_table_name', maxSizeInMb: 2_000),
]);
```

### Specifying the database connection

To check another database connection, call `connectionName()`

```php
DatabaseTableSizeCheck::new()->connectionName('another-connection-name'),
```
