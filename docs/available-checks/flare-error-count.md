---
title: Flare error count
weight: 12
---

This check will monitor the amount of errors and exceptions your application throws. For this check you'll need to have an account on [Flare](https://flareapp.io).

By default, this check will send:
- a warning when there were more than 500 errors reported to Flare in the past 60 minutes
- a failure when there were more than 1000 errors reported to Flare in the past 60 minutes

## Usage

To start using this check, you should have a Flare API Token and Flare project id. Check the [Flare docs](https://flareapp.io/docs) on how to get these values.

Here's how you can register the check.

```php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\FlareErrorOccurrenceCountCheck;

Health::checks([
    FlareErrorOccurrenceCountCheck::new()
        ->apiToken($flareApiToken)
        ->projectId($flareProjectId),
]);
```

## Customizing the amount of errors

To customize the amount of errors that triggers a warning/failure, you can call `warnWhenMoreErrorsReceivedThan`, and `failWhenMoreErrorsReceivedThan`.

```php
FlareErrorOccurrenceCountCheck::new()
    ->warnWhenMoreErrorsReceivedThan(20)
    ->failWhenMoreErrorsReceivedThan(50),
```
