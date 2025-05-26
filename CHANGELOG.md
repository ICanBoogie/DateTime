# CHANGELOG

## v3.0.0

### New Requirements

Requires PHP 8.2+

### New features

None

### Backward Incompatible Changes

None

### Deprecated Features

None

### Other Changes

- Compatible with PHP 8.4.
- Add static analysis with PHPStan.



## v2.0.0

### New Requirements

Requires PHP >=7.3 <8.4, for PHP 8.4+ use v3.0

### New features

None

### Backward Incompatible Changes

- `DateTime::__construct()` no longer accepts `DateTimeInterface` instances, only string, just like
  PHP's `DateTime`. Use `::from()` instead.

### Deprecated Features

None

### Other Changes

None
