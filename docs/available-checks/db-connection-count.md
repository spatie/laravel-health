---
title: DB connection count
weight: 7
---

This check makes sure your database doesn't have too much active connections. This check supports MySQL and Postgres.

If there are more than 50 connections, this check will fail. Of course, you can customize that amount.

## Requirements

You'll need to install the `doctrine/dbal` package in your project.

```bash
composer require doctrine/dbal
```


## Usage

Here's how you can register the check, and let if fail when there are more than 100 connections.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\DatabaseConnectionCountCheck;

Health::checks([
    DatabaseConnectionCountCheck::new()
        ->failWhenMoreConnectionsThan(100)
]);
```

Optionally, you can also specify a warning threshold.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\DatabaseConnectionCountCheck;

Health::checks([
    DatabaseConnectionCountCheck::new()
        ->warnWhenMoreConnectionsThan(50)
        ->failWhenMoreConnectionsThan(100)
]);
```

### Specifying the database connection

To check another database connection, call `connectionName()`

```php
DatabaseConnectionCountCheck::new()->connectionName('another-connection-name'),
```
