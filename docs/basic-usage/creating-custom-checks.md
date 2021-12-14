---
title: Creating custom checks
weight: 3
---

This package offers [a few common checks](https://spatie.be/docs/laravel-health/v1/available-checks/overview) right out of the box. If you want to monitor another aspect of your app, you can create your own custom check.

A check is any class that extends from `Spatie\Health\Checks\Check`. It contains one abstract method that you should implement:  `run`.

```php
namespace App\Checks;

use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

class YourCustomCheck extends Check
{
    public function run(): Result
    {
        // your custom logic...
    }
}
```

## Creating results

The `run` method should always return a `Spatie\Health\Checks\Result`. Using this object you can instruct the package to report a failure, to send a notification, and add meta information.

### Setting a status and sending notifications

The `Result` object has a `status` to signify a check is ok, produces a warning, or has failed.

You can use these methods to set the `status` of a result:

```php
$result = Spatie\Health\Checks\Result::make();

$result->ok(); // the check ran ok
$result->warning(); // the check ran ok, but with a warning
$result->failed(); // the check failed
```

You should call `ok()` when everything your check verifies is ok. You should call `fail()` if you detected that there was something wrong. The `warning()` should be used when the check did pass, but might fail soon.

When you pass a string to any of these methods and return that result, then the package will send a notification.

Here's an example check that will check for used disk space. When used disk space is above 70%, a warning notification with a warning will be sent, when above 90% an error notification will be sent.

```php
namespace App\Checks;

use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

class UsedDiskSpaceCheck extends Check
{
    public function run(): Result
    {
        $usedDiskSpacePercentage = $this->getDiskUsagePercentage();

        $result = Result::make();

        if ($usedDiskSpacePercentage > 90) {
            return $result->failed("The disk is almost full ({$usedDiskSpacePercentage} % used)");
        }

        if ($usedDiskSpacePercentage > 70) {
            return $result->warning("The disk getting full ({$usedDiskSpacePercentage}% used)");
        }

        return $result->ok();
    }

    protected function getDiskUsagePercentage(): int
    {
        // determine used disk space, omitted for brevity
    }
}
```

## Adding a short summary

Optionally, you can add a short summary of what the check found, using the `shortSummary` method.

```php
namespace App\Checks;

use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

public function run(): Result
{
    $usedDiskSpacePercentage = $this->getDiskUsagePercentage();

    $result = Result::make();
    
    $result->shortSummary("{$usedDiskSpacePercentage}%")

    // ...
}
```

This short summary will be written in [a result store](https://spatie.be/docs/laravel-health/v1/storing-results/general). The summary can be used when displaying all the results on a dashboard.

## Adding meta information

You can add meta information to a result. This meta information will be written in [a result store](https://spatie.be/docs/laravel-health/v1/storing-results/general). By adding meta information, external services like Oh Dear can display it in their notification, or you can keep a history of this meta information in your own database.

Meta information can be added to a check result, by calling `meta` and passing it with an array of meta data.

```php
namespace App\Checks;

use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

class UsedDiskSpaceCheck extends Check
{
    public function run(): Result
    {
        $usedDiskSpacePercentage = $this->getDiskUsagePercentage();

        $result = Result::make();
        
        $result->meta(['used_disk_space' => $usedDiskSpacePercentage]);
        
        // rest of the check omitted for brevity
        
        return $result;
    }
}
```

