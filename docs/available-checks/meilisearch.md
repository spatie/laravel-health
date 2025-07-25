---
title: Meilisearch
weight: 14
---
[meilisearch.md](meilisearch.md)
This check will verify if Meilisearch is running. It will call Meilisearch's [built-in health endpoint](https://docs.meilisearch.com/reference/api/health.html) and verify that its status returns `available`.

## Usage

Here's how you can register the check.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\MeilisearchCheck;

Health::checks([
    MeilisearchCheck::new(),
]);
```

### Customizing the endpoint

By default, the check will try to get a response from "https://127.0.0.1:7700/health".

You can use `url()` method to change that url.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\MeilisearchCheck;

Health::checks([
    MeilisearchCheck::new()->url("https://your-custom-url:1234/custom-endpoint"),
]);
```

### Customizing the timeout

By default, the check has a timeout of 1 second.

You can use `timeout()` to set the maximum number of seconds the HTTP request should run for before it fails.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\MeilisearchCheck;

Health::checks([
    MeilisearchCheck::new()->timeout(2),
]);
```

### Adding an authorization header

You can use `token()` to add an authorization header to the request.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\MeilisearchCheck;

Health::checks([
    MeilisearchCheck::new()->token('auth-token'),
]);
```
