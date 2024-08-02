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

### Check backups on external filesystems

You can use the `onDisk` method to specify any disk you have configured in Laravel.
This is useful when the backups are stored on an external filesystem.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\BackupsCheck;

Health::checks([
    BackupsCheck::new()
        ->onDisk('backups')
        ->locatedAt('backups'),
]);
```

Checking backup files on external filesystems can be slow if you have a lot of backup files.
* You can use the `parseModifiedFormat` method to get the modified date of the file from the name instead of reaching out to the file and read its metadata. This strips out the file folder and file extension and uses the remaining string to parse the date with `Carbon::createFromFormat`.
* You can also limit the size check to only the first and last backup files by using the `onlyCheckSizeOnFirstAndLast` method. Otherwise the check needs to reach out to all files and check the file sizes.

These two things can speed up the check of ~200 files on an S3 bucket from about 30 seconds to about 1 second.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\BackupsCheck;

Health::checks([
    BackupsCheck::new()
        ->onDisk('backups')
        ->parseModifiedFormat('Y-m-d_H-i-s'),
        ->atLeastSizeInMb(20),
        ->onlyCheckSizeOnFirstAndLast()
]);
```

For files that contains more than just the date you can use something like parseModifiedFormat('\b\a\c\k\u\p_Ymd_His')
which would parse a file with the name similar to `backup_20240101_120000.sql.zip`.
