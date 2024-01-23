---
title: CPU load
weight: 5
---

This check makes sure that your CPU load isn't too high.

## Installation

To use this check, you must install the [spatie/cpu-load-health-check](https://github.com/spatie/cpu-load-health-check) package

```bash
composer require spatie/cpu-load-health-check
```

## Usage

The package monitors the [Unix CPU load numbers](https://www.tecmint.com/understand-linux-load-averages-and-monitor-performance/). By default, three numbers are available:

- load in the last minute
- load in the last 5 minutes
- load in the last 15 minutes

You can make the check fail by using on of these three functions:

- `failWhenLoadIsHigherInTheLastMinute($load)`
- `failWhenLoadIsHigherInTheLast5Minutes($load)`
- `failWhenLoadIsHigherInTheLast15Minutes($load)`

Here's how you can register the check.

```php
use Spatie\Health\Facades\Health;
use Spatie\CpuLoadHealthCheck\CpuLoadCheck;

Health::checks([
    CpuLoadCheck::new()
        ->failWhenLoadIsHigherInTheLast5Minutes(2.0)
        ->failWhenLoadIsHigherInTheLast15Minutes(1.5),
]);
```
