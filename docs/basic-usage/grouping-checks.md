---
title: Grouping checks
weight: 6
---

Sometimes you might want to run just a certain group of checks. 
For example during the deployment, to ensure a working database connection before running the migrations but ignoring 
the status of Horizon. 

## Assigning checks to groups
Each check can be assigned to none, one or multiple groups.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\PingCheck;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\HorizonCheck;

Health::checks([
    PingCheck::new()->url('https://your-site.com'),
    DatabaseCheck::new()->group('before-migration'),
    HorizonCheck::new()->group(['after-deployment', 'other-group']),
])
```


## Running checks of a certain group
This command will only run checks assigned to the `before-migration` group, all other checks will be ignored.

```bash
php artisan health:check --fail-command-on-failing-check --group=before-migration
```

Running it without the `--group` option will run all registered checks.

```bash
php artisan health:check --fail-command-on-failing-check
```


