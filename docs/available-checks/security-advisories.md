---
title: Security advisories
weight: 20
---

This check will check if the PHP packages installed in your project have known security vulnerabilities. This check works using [Packagist's security vulnerability API](https://php.watch/articles/composer-audit#packagist-vuln-list-api). 

## Installation

You must first install the check using composer

```bash
composer require spatie/security-advisories-health-check
```

## Usage

To start using the check, you must register the `SecurityAdvisoriesCheck` class.

```php
use Spatie\Health\Facades\Health;
use Spatie\SecurityAdvisoriesHealthCheck\SecurityAdvisoriesCheck;

Health::checks([
    SecurityAdvisoriesCheck::new(),
]);
```

The check will pass if there are no security advisories for the packages currently installed in your project.

If security advisories are found, the check will fail. The failure message will contain the names of package that have security issues. In the `meta` key of history item of the check, the full vulnerability advisories will be saved. Alternatively, you can run `composer audit` in the root directory of your application to see a list of security issues.

### Ignoring packages

To ignore certain packages, you can use the `ignorePackage` method.

```php
Health::checks([
    SecurityAdvisoriesCheck::new()->ignorePackage('spatie/laravel-backup'),
]);
```

You can ignore multiple packages in one go with the `ignoredPackages` method.

```php
Health::checks([
    SecurityAdvisoriesCheck::new()->ignoredPackages([
       'spatie/laravel-backup',
       'spatie/laravel-medialibrary',
   ]),
]);
```
