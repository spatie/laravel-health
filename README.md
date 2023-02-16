# Check the health of your Laravel app

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-health.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-health)
[![run-tests](https://github.com/spatie/laravel-health/actions/workflows/run-tests.yml/badge.svg)](https://github.com/spatie/laravel-health/actions/workflows/run-tests.yml)
[![Check & fix styling](https://github.com/spatie/laravel-health/actions/workflows/pint.yml/badge.svg)](https://github.com/spatie/laravel-health/actions/workflows/pint.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-health.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-health)



Using this package you can monitor the health of your application by registering checks.

Here's an example where we'll monitor available disk space.

```php
// typically, in a service provider

use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;

Health::checks([
    UsedDiskSpaceCheck::new()
        ->warnWhenUsedSpaceIsAbovePercentage(70)
        ->failWhenUsedSpaceIsAbovePercentage(90),
]);
```

When the used disk space is over 70%, then a notification with a warning will be sent. If it's above 90%, you'll get an error notification. Out of the box, the package can notify you via mail and Slack.

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/laravel-health.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/laravel-health)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Documentation

All documentation is available [on our documentation site](https://spatie.be/docs/laravel-health).

## Alternatives

If you don't like our package, do try out one of these alternatives:

- [ans-group/laravel-health-check](https://github.com/ans-group/laravel-health-check)
- [Antonioribeiro/health](https://github.com/antonioribeiro/health)

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
