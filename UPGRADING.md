# Native status enum (unreleased, major version required)

Replacing `spatie/enum` with a native PHP enum is a source/API breaking change.
Do not release this change as a minor or patch version. Keeping the persisted
lowercase strings does not make application code backward compatible.

## Status API

- Replace `Status::ok()`, `warning()`, `failed()`, `crashed()`, and `skipped()` with
  `Status::Ok`, `Warning`, `Failed`, `Crashed`, and `Skipped`.
- Replace `Status::make($value)` with `Status::from($value)` and `equals()` or
  dynamic predicates such as `isOk()` with strict enum comparisons.
- Replace string casts, interpolation, concatenation, and `->label` with
  `->value`. Native enums are not `Stringable`. Their `->name` is PascalCase,
  not the old lowercase label.
- Replace `toArray()`, `toValues()`, and `toLabels()` with mappings over
  `Status::cases()`. `cases()` still returns all five statuses.
- `from()` and `tryFrom()` accept backing scalar values, not enum instances or
  arbitrary stringable objects. `from()` throws `ValueError` for an unknown
  string instead of `BadMethodCallException`; invalid argument types can throw
  `TypeError`. Values are case-sensitive: `from('OK')` no longer resolves to
  `ok`. Do not pass an existing status back through `from()`.
- `Status` no longer extends `Spatie\Enum\Enum` or implements `JsonSerializable`.
  Update those type hints and interface checks. `json_encode($status)` still
  produces the lowercase string, but the `jsonSerialize()` method is gone.
- Native enums cannot be constructed with `new`, cloned, or subclassed. Custom
  `Status` subclasses, overridden labels/values, and additional statuses must be
  redesigned. A compatibility factory would not restore these capabilities.
- PHP-serialized objects containing the old enum are not compatible with the
  native enum representation. Drain affected queued jobs and invalidate custom
  serialized caches before deploying. Built-in cache storage uses JSON instead.
- If application code uses `spatie/enum` independently, require it explicitly;
  this package no longer installs it.

## Results and custom result stores

`Result::$status` and its constructor still refer to `Status`, but that class is
now the native enum. `Result::make()`, `ok()`, `warning()`, and `failed()` remain
available.

`StoredCheckResult::$status`, its constructor, `make()`, and `status()` now require
`Status` instead of a string. This includes named arguments, direct property
assignment, and subclass overrides. Custom `ResultStore` implementations must
convert strings at their storage boundary with `Status::from($value)` and expose
enum instances in returned results. Use `->value` when writing string formats.
`containsCheckWithStatus()` accepts a status or an array of statuses, not strings.

A stored result without an explicit status now defaults to `Status::Failed`,
not an empty string. It must not report a check as healthy before a status has
been supplied. JSON reads normalize missing, null, empty, and unknown string
statuses to `Failed`; they no longer preserve arbitrary status strings. Valid
`ok`, `warning`, `failed`, `crashed`, and `skipped` values round trip unchanged.

## Eloquent models

`HealthCheckResultHistoryItem::$status` now reads as `Status`, not a string.
Use `$item->status === Status::Failed` or `$item->status->value`. Eloquent still
accepts valid backing strings on assignment and serializes the cast to a string
in `toArray()` and `toJson()`. Raw database values remain lowercase strings.

Custom history models must retain the `status => Status::class` cast when
overriding `$casts` or `casts()`. The public `$casts` property is retained. Custom
accessors or casts returning strings must be updated. Unknown historical database
values now throw `ValueError` when read through the enum cast; audit nonstandard
values before upgrading. No database migration is needed for the five built-in
statuses.

## Published views and presentation extensions

Update published `health::list-cli` and `health::status-indicator` views. The
`$color`, `$backgroundColor`, `$iconColor`, and `$icon` closures are no longer
provided. Read colors/icons through the status enum's `getCliColor()`,
`getBackgroundColor()`, `getIconColor()`, and `getIcon()` methods instead, and
render text using `->value`.

The protected `ListHealthChecksCommand::getBackgroundColor()` and
`StatusIndicator::getBackgroundColor()`, `getIconColor()`, and `getIcon()` hooks
were removed. Subclass overrides no longer customize rendering and parent calls
fail. Move those customizations to your own views or components; native enum
methods cannot be overridden by subclassing.

The JSON endpoint's schema, lowercase status values, and configured failure HTTP
codes remain unchanged for built-in statuses. This persistence compatibility is
separate from the PHP API changes above.
