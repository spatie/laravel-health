---
title: General
weight: 1
---

When `RunHealthChecksCommand` executes, all checks will be run. The results of these checks can be stored. Out of the box, this package offers two stores:

- `EloquentHealthResultStore`: will store all results in the database
- `CacheHealthResultStore`: will store all results in cache
- `JsonFileHealthResultStore`: will store all results in a JSON file.
- `InMemoryHealthResultStore`: will not store results at all

You can configure the stores to be used, by adding the store class name to the `result_stores` key of the `health` config file. 

Keep in mind, that you can also opt not to store the results at all, but only get notifications when something is wrong.
