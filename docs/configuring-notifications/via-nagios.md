---
title: Via Nagios
weight: 5
---

[Nagios](https://www.nagios.org/) is a popular open-source monitoring system that helps organizations monitor their IT infrastructure, including servers, applications, services, and network devices. It provides alerting capabilities when problems are detected and can send notifications via email, SMS, or other methods.

The package provides a dedicated endpoint for Nagios monitoring systems. This endpoint returns health check results in a format that Nagios can understand, allowing you to integrate your Laravel application's health status into your existing Nagios monitoring infrastructure.

## Enabling the Nagios endpoint

To enable the Nagios monitoring endpoint, you need to configure it in your `config/health.php` file:

```php
// in config/health.php

'nagios_endpoint' => [
    'enabled' => true,

    /*
     * When this option is enabled, the checks will run before sending a response.
     * Otherwise, we'll send the results from the last time the checks have run.
     */
    'always_send_fresh_results' => true,

    /*
     * The secret that is sent as auth bearer token.
     */
    'bearer_token' => env('NAGIOS_HEALTH_BEARER_TOKEN'),

    /*
     * The URL that should be configured nagios to check for application health.
     */
    'url' => '/health/nagios',
],
```

## Configuration options

### Bearer Token Authentication

For security, you should configure a bearer token that Nagios will use to authenticate with your endpoint:

```bash
# Add to your .env file
NAGIOS_HEALTH_BEARER_TOKEN=your-secret-token-here
```

### Custom Endpoint URL

You can customize the endpoint URL by changing the `url` configuration:

```php
'url' => '/custom/nagios/health',
```

## Response Format

The Nagios endpoint returns responses in the standard Nagios plugin format:

- **OK**: All health checks are passing
- **WARNING**: Some checks have warnings but no critical failures
- **CRITICAL**: One or more checks have failed critically
- **UNKNOWN**: No health check results are available

Example responses:

```
OK: All health checks are passing (5 checks)
WARNING: Database connection slow (1 warning, 4 ok)
CRITICAL: Queue connection failed (1 critical, 3 ok, 1 warning)
```

## Testing the Endpoint

You can test the endpoint manually to ensure it's working correctly:

```bash
# Without authentication (if bearer_token is not set)
curl https://your-app.com/health/nagios

# With bearer token authentication
curl -H "Authorization: Bearer your-secret-token-here" https://your-app.com/health/nagios

# Force fresh results
curl -H "Authorization: Bearer your-secret-token-here" https://your-app.com/health/nagios?fresh=1
```

## Troubleshooting

### Authentication Issues

If you're getting 401 Unauthorized responses:
- Verify the bearer token is correctly set in your `.env` file
- Ensure Nagios is sending the token in the correct format: `Authorization: Bearer your-token`

### No Results Available

If you see "UNKNOWN: No health check results available":
- Run the health checks manually: `php artisan health:check`
- Verify your health checks are properly configured
- Check that the scheduled health check command is running
