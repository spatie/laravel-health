---
title: General
weight: 1
---

Whenever one of the checks fails, the package can send you a notification. A notification will be sent for all checks that send a message in [their result](/docs/laravel-health/v1/basic-usage/creating-custom-checks#creating-results).

Out of the box, the notification can be sent:

- via [mail](/docs/laravel-health/v1/configuring-notifications/via-mail), 
- via [Slack](/docs/laravel-health/v1/configuring-notifications/via-slack)
- via [Oh Dear](/docs/laravel-health/v1/configuring-notifications/via-oh-dear) (enables snoozing notifications, and delivery via Telegram, Discord, MS Teams, webhooks, ...)

## Throttling notifications

These notifications are throttled, so you don't get overwhelmed with notifications when something goes wrong. By default, you'll only get one notification per hour.

In the `throttle_notifications_for_minutes` key of the `health` config file, you can customize the length of the throttling period.
