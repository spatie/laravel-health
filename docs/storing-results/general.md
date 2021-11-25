---
title: General
weight: 1
---

When `RunChecksCommand` executes, all checks will be run. The results of these checks can be stored. Out of the box, this package offers two stores:

- `EloquentHealthResultStore`: will store all results in the database
- `JsonFileHealthResultStore`: will store all results in a JSON file.

You can configure the store to be used, by adding the store class name to the `result_stores` key of the `health` config file.
