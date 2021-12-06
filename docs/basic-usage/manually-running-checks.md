---
title: Manually running checks
weight: 4
---

All registered checks will run automatically when the `RunHealthChecksCommand` executes. If you followed [the installation instructions](https://spatie.be/docs/laravel-health/v1/installation-setup), you have already scheduled that command to execute every minute.

You can also decide to manually run the command with:

```bash
php artisan health:check
```

Use the command like that will, also stored results and send notifications. Use one or more of the options below to avoid that.

## Fail the command when a check fails

By default, the `RunHealthChecksCommand` will always return a successful exit code (`1`). When you pass the `--fail-command-on-failing-check`, then the exit code of the command will be non-successful (`0`) when one of the checks fails.

```bash
php artisan health:check --fail-command-on-failing-check
```

## Avoid sending notifications

The `RunHealthChecksCommand` will send a notification when one of the checks fails. If you want to avoid sending a notification, you can pass the `--no-notification` option.

```bash
php artisan health:check --no-notification
```

## Avoid storing results

If you've configured [a result store](https://spatie.be/docs/laravel-health/v1/storing-results/general), then `RunHealthChecksCommand` will store the results. If you want to avoid storing results for a manual run, your can use the `--do-not-store-results` option.

```bash
php artisan health:check --do-not-store-results
```
