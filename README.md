# DateTime

[![Release](https://img.shields.io/packagist/v/icanboogie/datetime.svg)](https://packagist.org/packages/icanboogie/datetime)
[![Code Quality](https://img.shields.io/scrutinizer/g/ICanBoogie/DateTime.svg)](https://scrutinizer-ci.com/g/ICanBoogie/DateTime)
[![Code Coverage](https://img.shields.io/coveralls/ICanBoogie/DateTime.svg)](https://coveralls.io/r/ICanBoogie/DateTime)
[![Downloads](https://img.shields.io/packagist/dt/icanboogie/datetime.svg)](https://packagist.org/packages/icanboogie/datetime)

A library to work with date and time.


### Usage

```php
<?php

namespace ICanBoogie\DateTime;

require_once 'vendor/autoload.php';

//
// Time
//

$time = LocalTime::from("19:10:20.123");
# or
$time = new LocalTime(19, 10, 20, 123);

echo PHP_EOL . $time;                                                     // 19:10:20.123000
echo PHP_EOL . $time->hour;                                               // 19
echo PHP_EOL . $time->minute;                                             // 10
echo PHP_EOL . $time->second;                                             // 20
echo PHP_EOL . $time->microsecond;                                        // 123000
echo PHP_EOL . $time->toSecondOfDay();                                    // 69020
echo PHP_EOL . $time->toMillisecondOfDay();                               // 69020123
echo PHP_EOL . $time->toMicrosecondOfDay();                               // 69020123000
echo PHP_EOL . $time->format(LocalTime::FORMAT_WITH_MICROSECONDS);        // 19:10:20.123000
echo PHP_EOL . $time->format(LocalTime::FORMAT_WITHOUT_MICROSECONDS);     // 19:10:20

$interval = new \DateInterval("PT1H");
$before = $date->sub($interval);
$after = $date->add($interval);

echo PHP_EOL . $before;                                                   // 20:10:20.123000
echo PHP_EOL . $after;                                                    // 18:10:20.123000
echo PHP_EOL . ($time <=> $before);                                       // 1
echo PHP_EOL . ($time <=> $after);                                        // -1

//
// Date
//

$date = LocalDate::from("2024-03-20");
# or
$date = new LocalDate(2024, 3, 20);
# or
$date = new LocalDate(2024, Month::MARCH, 20);

echo PHP_EOL . $date;                                                     // 2024-03-20
echo PHP_EOL . $date->year;                                               // 2024
echo PHP_EOL . $date->month->name;                                        // MARCH
echo PHP_EOL . $date->monthNumber;                                        // 3
echo PHP_EOL . $date->dayOfMonth;                                         // 20
echo PHP_EOL . $date->dayOfWeek->name;                                    // WEDNESDAY
echo PHP_EOL . $date->dayOfYear;                                          // 80

$interval = new \DateInterval("P1M");
$before = $date->sub($interval);
$after = $date->add($interval);

echo PHP_EOL . $before;                                                   // 2024-02-20
echo PHP_EOL . $after;                                                    // 2024-04-20
echo PHP_EOL . ($date <=> $before);                                       // 1
echo PHP_EOL . ($date <=> $after);                                        // -1
```



#### Installation

```bash
composer require icanboogie/datetime
```



## Types

The library provides a basic set of types for working with date and time. All types are immutable.



### Type use-cases

Here is some basic advice on how to choose which of the date-carrying types to use in what cases:

- Use `LocalDateTime` to represent
- Use `LocalDate` to represent the date of an event that does not have a specific time associated with it (like a birth date).
- Use `LocalTime` to represent the time of an event that does not have a specific date associated with it.



## Compatibility with PHP's DateTime

`LocalDateTime`, `LocalDate`, and `LocalTime` do not implement `DateTimeInterface`, but they all maintain a `DateTimeImmutable` that used as a delegate for operations.

```php
<?php

namespace ICanBoogie\DateTime;

Time::from("now")->delegate;     // A DateTimeImmutable with date components set to 0
Date::from("now")->delegate;     // A DateTimeImmutable with time components set to 0
DateTime::from("now")->delegate; // A DateTimeImmutable
```



----------



## Continuous Integration

The project is continuously tested by [GitHub actions](https://github.com/ICanBoogie/DateTime/actions).

[![Tests](https://github.com/ICanBoogie/DateTime/workflows/test/badge.svg)](https://github.com/ICanBoogie/DateTime/actions?query=workflow%3Atest)
[![Static Analysis](https://github.com/ICanBoogie/DateTime/workflows/static-analysis/badge.svg)](https://github.com/ICanBoogie/DateTime/actions?query=workflow%3Astatic-analysis)
[![Code Style](https://github.com/ICanBoogie/DateTime/workflows/code-style/badge.svg)](https://github.com/ICanBoogie/DateTime/actions?query=workflow%3Acode-style)



## Code of Conduct

This project adheres to a [Contributor Code of Conduct](CODE_OF_CONDUCT.md). By participating in
this project and its community, you are expected to uphold this code.



## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.



## License

**ICanBoogie/DateTime** is released under the [BSD-3-Clause](LICENSE).
