---
title: As JSON
weight: 2
---

When using the `Spatie\Health\ResultStores\JsonFileHealthResultStore` result store the latest check results will be written to a JSON file. 

In the `health` config file, the store can be configured in the `health_stores` key like this:

```php
return [
    'result_stores' => [
        JsonFileHealthResultStore::class => [
            'disk' => 's3',
            'file_name' => 'health.json',
        ],
    ],
```

The `disk` key can be set to any of the disks you configured in `config/filesystems.php`. Using an external disk, like `s3`, has the benefit that, should your application go down, you can still see the latest results via an S3 url. In case your app is fully down, external services monitoring your health file, should notice that the `finishedAt` key in the contents will not be updated anymore.

You can parse this file get detailed information of how the checks have run. This can be used as the basis for a dashboard, or you could let an external service monitor the contents of this file. The JSON contains two top level properties:

- `finishedAt`: the datetime when all checks have finished running
- `checkResults`: the results of all checks

Here's an example of how the content could look like:

```json
{
    "finishedAt": "2021-01-01 12:25:12",
    "checkResults": [
        {
            "name": "UsedDiskSpace",
            "message": "The disk getting full ({$usedDiskSpacePercentage}% used)",
            "status": "warning",
            "meta": {
                "disk_space_used_percentage": 75
            }
        }
    ]
}
```
