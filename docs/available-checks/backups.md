---
title: Backups
weight: 2
---

Using a package like [spatie/laravel-backup](https://spatie.be/docs/laravel-backup) you can create backups of your application. The backups are stored as zip files in a directory.

The `BackupCheck` will verify if your backups are up to date. It can check:
- if the youngest backup has been made before a certain date
- if the oldest backup was made after a certain date
- the number of backups
- the size of the backups

## Usage

Here's how you can register the check.

You can use the `locatedAt` method to specify the directory where the backups are stored. The `locatedAt` method accepts a glob pattern.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\BackupsCheck;

Health::checks([
    BackupsCheck::new()->locatedAt('/path/to/backups/*.zip'),
]);
```

### Check the number of the backups

You can use the `numberOfBackups` method to check if the number of backups is within a certain range.
The function accepts a `min` and/or a `max` parameter.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\BackupsCheck;

Health::checks([
    BackupsCheck::new()->locatedAt('/path/to/backups/*.zip')->numberOfBackups(min: 5, max: 10),
]);
```

### Check the age of the backups

You can use the `youngestBackShouldHaveBeenMadeBefore` method to check if the youngest backup was made before a certain date.

Here's an example where we make sure the most recent backup is not older than 1 day.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\BackupsCheck;

Health::checks([
    BackupsCheck::new()
        ->locatedAt('/path/to/backups/*.zip')
        ->youngestBackShouldHaveBeenMadeBefore(now()->subDays(1)),
]);
```

You can use the `oldestBackShouldHaveBeenMadeAfter` method to check if the oldest backup was made after a certain date.

Here's an example where we make sure the oldest backup is older than 1 week.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\BackupsCheck;

Health::checks([
    BackupsCheck::new()
        ->locatedAt('/path/to/backups/*.zip')
        ->oldestBackShouldHaveBeenMadeAfter(now()->subWeeks(1)),
]);
```

### Specify a minimum size

You can use the `atLeastSizeInMb` method to specify a minimum size for the backups. Only backups that are larger than the specified size will be considered valid.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\BackupsCheck;

Health::checks([
    BackupsCheck::new()
        ->locatedAt('/path/to/backups/*.zip')
        ->atLeastSizeInMb(20),
]);
```
