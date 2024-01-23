---
title: Environment
weight: 11
---

This check will make sure your application is running used the right environment. By default, this check will fail when the environment is not equal to `production`.

## Usage

Here's how you can register the check.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\EnvironmentCheck;

Health::checks([
    EnvironmentCheck::new(),
]);
```


### Customizing the environment

To customize the expected environment, call `expectEnvironment`.

```php
EnvironmentCheck::new()->expectEnvironment('staging'),
```
