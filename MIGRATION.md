# Migration

## v1.x to v2.x

### New Requirements

Requires PHP 7.3+

### New features

None

### Backward Incompatible Changes

- `DateTime::__construct()` no longer accepts `DateTimeInterface` instances, only string, just like
  PHP's `DateTime`. Use `::from()` instead.

### Deprecated Features

None

### Other Changes

None
