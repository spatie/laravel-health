---
title: Testing
weight: 5
---

You may wish to test that your checks are handled correctly, without needing to satisfy the
requirements of the check itself. The package provides the ability to fake the results of
checks.

## Basic usage

The `Health` Facade has a `fake` method that allows you to fake the results of a check. Let's use it
to make the `DatabaseCheck` fail in a test:

```php
it('has an error if the database is not available', function () {
  Health::fake([
    DatabaseCheck::class => new Result(Status::failed())
  ]);

  $this->get('/health')->assertStatus(503);
});
```

Even if the database is available, the health endpoint will fail thanks to our fake. You may fake as many checks
as you'd like in the array passed to the `fake` method.

## Advanced usage

There may be occasions where you need to override the `shouldRun` method in a fake. To accomplish this,
you may call `FakeCheck::result` and pass a boolean indicating whether the check should run:

```php
it('has an error if the database is not available', function () {
  Health::fake([
    DatabaseCheck::class => FakeCheck::result(
        new Result(Status::failed()),
        true // Run this check, even if `shouldRun` returns false in the check itself
    )
  ]);

  $this->get('/health')->assertStatus(503);
});
```

On rare occasions, you may have multiple instances of the same check, and wish to return different
results for each. The package allows you to provide a closure, which will receive the instance of
the check as an argument:

```php
it('has an error if the database is not available', function () {
  Health::fake([
    DatabaseCheck::class => fn($check) => $check->getName() === 'Users DB' 
        ? new Result(Status::ok())
        : new Result(Status::failed())
  ]);

  $this->get('/health')->assertStatus(503);
});
```
