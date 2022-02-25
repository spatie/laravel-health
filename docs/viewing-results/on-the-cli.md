---
title: On the CLI
weight: 3
---

To view the results of all checks, you can run this artisan command:

```bash
php artisan health:list
```

![image](/docs/laravel-health/v1/images/list-cli.png)

If you want to first execute all the checks, so you'll see fresh results, add the `fresh` option.

```bash
php artisan health:list --fresh
```

When using the `run` option, you can also use the `do-not-store-results` and  `no-notification` options, to avoid storing results and avoid sending a notification.

```bash
php artisan health:list --fresh --do-not-store-results --no-notification
```

By default, if some check is failing, this artisan command will return a zero exit status.
If you want it to return a non-zero exit code, just use the `--fail-command-on-failing-check` option. 

```bash
php artisan health:list --fail-command-on-failing-check
```
