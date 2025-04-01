---
title: Using Secret Token
weight: 1
---

If you want a simple way to protect an endpoint from unwanted access over the internet, you can use the Secret Token feature. This allows you to restrict access by requiring a predefined token to be included in requests, ensuring that only authorized clients can interact with the endpoint.

## Usage

Here's how you can use Secret Token to protect an endpoint:

Define secret token in your `.env` file:

```env
HEALTH_SECRET_TOKEN=your-secret-token
```

Use `RequiresSecretToken` middleware in your route:

```php
Route::get('/', HealthCheckJsonResultsController::class)->middleware(RequiresSecretToken::class);
```

Add `X-Secret-Token` in the request header:

```
X-Secret-Token: your-secret-token
```

## Warning
Secret Token is a simple way to protect an endpoint from unwanted access over the internet. However, it is not a foolproof security measure. If you need a more secure solution, consider using a more advanced authentication method.

