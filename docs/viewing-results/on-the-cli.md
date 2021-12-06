---
title: On the CLI
weight: 3
---

To view the results of all checks, you can run this artisan command:

```bash
php artisan health:list
```

If you want to first execute all the checks, so you'll see fresh results, add the `run` option.

```bash
php artisan health:list --run
```

When using the `run` option, you can also use the `do-not-store-results` and  `no-notification` options, to avoid storing results and avoid sending a notification.

```bash
php artisan health:list --run --do-not-store-results --no-notification
```

