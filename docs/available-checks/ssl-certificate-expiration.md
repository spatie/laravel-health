---
title: SSL certificate expiration
weight: 13
---

This third party check makes sure that your SSL certificate isn't expired.

## Installation

To use this check, you must install the [spatie/cpu-load-health-check](https://github.com/victord11/ssl-certification-health-check) package

```bash
composer require victord11/ssl-certification-health-check
```

## Usage

Here's how you can register the check.

```php
use Spatie\Health\Facades\Health;
use VictoRD11\SslCertificationHealthCheck\SslCertificationExpiredCheck;
use VictoRD11\SslCertificationHealthCheck\SslCertificationValidCheck;

Health::checks([
    SslCertificationExpiredCheck::new()
        ->url('google.com')
        ->warnWhenSslCertificationExpiringDay(24)
        ->failWhenSslCertificationExpiringDay(14),
]);
```
