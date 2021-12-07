---
title: Via mail
weight: 2
---

When a check starts returning warnings or failures, you can get notified via mail.

To do this you must set the `CheckFailedNotification` to use the `mail` channel. This can be done in the config file.

```php
// in config/health.php

'notifications' => [
    Spatie\Health\Notifications\CheckFailedNotification::class => ['mail'],
],
```

In the `mail` key of the `health` config file, you can configure the to/from of the sent mails.

```php
// in config/health.php

'mail' => [
    'to' => 'your@example.com',

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name' => env('MAIL_FROM_NAME', 'Example'),
    ],
],
```

Here's how the mail will look like (rendered via [Ray](https://myray.app)).

![image](/docs/laravel-health/v1/images/mail.png)
