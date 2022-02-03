---
title: Typesense
weight: 9
---

This check will verify if Typesense is running. It will call Typesense's [built-in health endpoint](https://typesense.org/docs/guide/install-typesense.html#%F0%9F%86%97-health-check) and verify that its response returns `{"ok":true}`.

## Usage

Here's how you can register the check.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\TypesenseCheck;

Health::checks([
    TypesenseCheck::new(),
]);
```

### Customizing the endpoint

By default, the check will try to get a response from "http://127.0.0.1:8108/health".

You can use `url()` method to change that url.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\TypesenseCheck;

Health::checks([
    TypesenseCheck::new()->url("https://your-custom-host:port/health"),
]);
```

### Customizing the timeout

By default, the check has a timeout of 1 second.

You can use `timeout()` to set the maximum number of seconds the HTTP request should run for before it fails.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\TypesenseCheck;

Health::checks([
    TypesenseCheck::new()->timeout(2),
]);
```
