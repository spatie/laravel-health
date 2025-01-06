---
title: Pausing and resuming checks
weight: 5
---

You might want to temporarily pause checks to avoid unnecessary alerts during deployments or maintenance. This is particularly useful when services might experience brief downtime, which could otherwise trigger false alarms.

## Pause Checks

You can pause checks for a specified duration. By default, checks will be paused for 300 seconds (5 minutes).

```bash
# Pause checks for the default duration (300 seconds)
php artisan health:pause

# Pause checks for a custom duration (in seconds)
php artisan health:pause 60
```

During the pause period, checks will not run, and no alerts will be triggered.

## Resume Checks

If you need to resume checks before the pause duration ends, you can do so with the following command:

```bash
# Resume checks immediately
php artisan health:resume
```
