---
title: DB size
weight: 8
---

This check makes sure that your database is not too big. This check supports MySQL and Postgres.

If the database is larger than the specified maximum, this check will fail.

## Requirements

You'll need to install the `doctrine/dbal` package in your project.

```bash
composer require doctrine/dbal
```


## Usage

Here's how you can register the check.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\DatabaseSizeCheck;

Health::checks([
    DatabaseSizeCheck::new()
        ->failWhenSizeAboveGb(errorThresholdGb: 5.0)
]);
```

### Specifying the database connection

To check another database connection, call `connectionName()`

```php
DatabaseSizeCheck::new()->connectionName('another-connection-name')->failWhenSizeAboveGb(5.0),
```
