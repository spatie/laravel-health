---
title: General
weight: 1
---

This package provides several ways of listing the latest check results.

- [on a webpage](/docs/laravel-health/v1/viewing-results/on-a-webpage)
- [on the cli](/docs/laravel-health/v1/viewing-results/on-the-cli)
- [as JSON](/docs/laravel-health/v1/viewing-results/as-json)


## Customizing the titles of the checks

You can use the `label` function to customize the title that is shown on the health dashboard, and on the Oh Dear application health screen.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;

Health::checks([
    UsedDiskSpaceCheck::new()->label('Disk space on main disk'),
]);
```
