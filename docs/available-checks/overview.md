---
title: Overview
weight: 1
---

Using this package you can register one or more checks to verify the health of your application.

These are the checks created by us:

- [Application Cache](cache)
- [Backups](backups)
- [CPU Load](cpu-load)
- [Database Connection](db-connection)
- [Database Connection Count](db-connection-count)
- [Database Table Size](db-table-size-check)
- [Debug Mode](debug-mode)
- [Environment](environment)
- [Flare Error Count](flare-error-count)
- [Horizon](horizon)
- [MeiliSearch](meilisearch)
- [Ping](ping)
- [Queue](queue)
- [Redis](redis)
- [Schedule](schedule)
- [Security advisories](security-advisories)
- [Used Disk Space](used-disk-space)

## Third party checks

If you have created [a custom check](/docs/laravel-health/v1/basic-usage/creating-custom-checks), consider packaging it up so others can make use of it too. Take a look at the [spatie/cpu-load-health-check](https://github.com/spatie/cpu-load-health-check) for a good example of how to package a health check. If you don't know how to create a package, consider watching the [Laravel Package Training](https://laravelpackage.training).

Here's a list of third party packages:

- [Env vars](https://github.com/encodia/laravel-health-env-vars)
- [SSL certificate expiration](https://github.com/victord11/ssl-certification-health-check)
- [Laravel Octane](https://github.com/ahtinurme/octane-health-check)
- [Queue Size Check](https://github.com/SRWieZ/queue-size-health-check)
- [Opcache Check](https://github.com/f9webltd/laravel-health-opcache)

