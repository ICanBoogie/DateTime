# DateTime

[![Release](https://img.shields.io/packagist/v/ICanBoogie/DateTime.svg)](https://packagist.org/packages/icanboogie/datetime)
[![Build Status](https://img.shields.io/travis/ICanBoogie/DateTime.svg)](http://travis-ci.org/ICanBoogie/DateTime)
[![HHVM](https://img.shields.io/hhvm/icanboogie/datetime.svg)](http://hhvm.h4cc.de/package/icanboogie/datetime)
[![Code Coverage](https://img.shields.io/coveralls/ICanBoogie/DateTime.svg)](https://coveralls.io/r/ICanBoogie/DateTime)
[![Packagist](https://img.shields.io/packagist/dt/icanboogie/datetime.svg)](https://packagist.org/packages/icanboogie/datetime)

This package extends the features of PHP [DateTime](http://www.php.net/manual/en/class.datetime.php)
and [DateTimeZone](http://www.php.net/manual/en/class.datetimezone.php) classes to ease the
handling of times, time zones and time zone locations. Getting the UTC or local representation of
a time, formatting the time to a predefined format, accessing common properties such as day, month,
year, quarter and more has been made especially easy. Also, all instances can be used as strings.





### Usage

Let's say that _now_ is "2013-02-03 21:03:45" in Paris:

```php
<?php

use ICanBoogie\DateTime;

date_default_timezone_set('EST'); // set local time zone to Eastern Standard Time

$time = new DateTime('now', 'Europe/Paris');

echo $time;                             // 2013-02-03T21:03:45+0100
echo $time->utc;                        // 2013-02-03T20:03:45Z
echo $time->local;                      // 2013-02-03T15:03:45-0500
echo $time->utc->local;                 // 2013-02-03T15:03:45-0500
echo $time->utc->is_utc;                // true
echo $time->utc->is_local;              // false
echo $time->local->is_utc;              // false
echo $time->local->is_local;            // true
echo $time->is_dst;                     // false

echo $time->as_rss;                     // Sun, 03 Feb 2013 21:03:45 +0100
echo $time->as_db;                      // 2013-02-03 21:03:45

echo $time->as_time;                    // 21:03:45
echo $time->utc->as_time;               // 20:03:45
echo $time->local->as_time;             // 15:03:45
echo $time->utc->local->as_time;        // 15:03:45

echo $time->quarter;                    // 1
echo $time->week;                       // 5
echo $time->day;                        // 3
echo $time->minute;                     // 3
echo $time->is_monday;                  // false
echo $time->is_saturday;                // true
echo $time->is_today;                   // true
echo $time->tomorrow;                   // 2013-02-04T00:00:00+0100
echo $time->tomorrow->is_future         // true
echo $time->yesterday;                  // 2013-02-02T00:00:00+0100
echo $time->yesterday->is_past          // true
echo $time->monday;                     // 2013-01-28T00:00:00+0100
echo $time->sunday;                     // 2013-02-03T00:00:00+0100

echo $time->timestamp;                  // 1359921825
echo $time;                             // 2013-02-03T21:03:45+0100
$time->timestamp += 3600 * 4;
echo $time;                             // 2013-02-04T01:03:45+0100

echo $time->zone;                       // Europe/Paris
echo $time->zone->offset;               // 3600
echo $time->zone->location;             // FR,48.86667,2.33333
echo $time->zone->location->latitude;   // 48.86667
$time->zone = 'Asia/Tokyo';
echo $time;                             // 2013-02-04T09:03:45+0900

$time->hour += 72;
echo "Rendez-vous in 72 hours: $time";  // Rendez-vous in 72 hours: 2013-02-07T05:03:45+0900
```

Empty dates are also supported:

```php
<?php

use ICanBoogie\DateTime;

$time = new DateTime('0000-00-00', 'utc');
// or
$time = DateTime::none();

echo $time->is_empty;                   // true
echo $time->as_date;                    // 0000-00-00
echo $time->as_db;                      // 0000-00-00 00:00:00
echo $time;                             // ""
```





### Acknowledgements

The implementation of the [DateTime][] class is vastly inspired by Ruby's
[Time](http://www.ruby-doc.org/core-1.9.3/Time.html) class.





## Day of week

```php
<?php

use ICanBoogie\DateTime:

$time = new DateTime('2014-01-06 11:11:11', 'utc'); // a monday at 11:11:11 UTC

echo $time->monday;                          // 2014-01-06T00:00:00Z
echo $time->tuesday;                         // 2014-01-07T00:00:00Z
echo $time->wednesday;                       // 2014-01-08T00:00:00Z
echo $time->thursday;                        // 2014-01-09T00:00:00Z
echo $time->friday;                          // 2014-01-10T00:00:00Z
echo $time->saturday;                        // 2014-01-11T00:00:00Z
echo $time->sunday;                          // 2014-01-12T00:00:00Z

$time->monday->is_monday;                    // true
$time->tuesday->is_tuesday;                  // true
$time->wednesday->is_wednesday;              // true
$time->thursday->is_thursday;                // true
$time->friday->is_friday;                    // true
$time->saturday->is_saturday;                // true
$time->sunday->is_sunday;                    // true

$time->monday->is_tuesday;                   // false
$time->tuesday->is_wednesday;                // false
$time->wednesday->is_thursday;               // false
$time->thursday->is_friday;                  // false
$time->friday->is_saturday;                  // false
$time->saturday->is_sunday;                  // false
$time->sunday->is_monday;                    // false

$time->monday->weekday;                      // 1
$time->tuesday->weekday;                     // 2
$time->wednesday->weekday;                   // 3
$time->thursday->weekday;                    // 4
$time->friday->weekday;                      // 5
$time->saturday->weekday;                    // 6
$time->sunday->weekday;                      // 7
```





## `now()` and `right_now()`

`DateTime::now()` returns a new instance with the current local time and the local time zone. Subsequent calls return equal times, event if they are minutes apart. _now_ actually refers to the `REQUEST_TIME` or, if it is not available, to the first time the method was invoked.

On the other hand, `DateTime::right_now()` returns a new instance with the _real_ current local time and the local time zone.

The following example demonstrates the difference:

```php
<?php

use ICanBoogie\DateTime;

$now = DateTime::now();

sleep(2);

$now == DateTime::now();         // true
$now == DateTime::right_now();   // false
```





## Comparing DateTime instances

[DateTime][] Instances are compared using standard comparison operations:

```php
<?php

use ICanBoogie\DateTime;

$d1 = DateTime::now();
$d2 = DateTime::now();

$d1 == $d2; // true
$d1 >= $d2; // true
$d1 <= $d2; // true
$d1 != $d2; // false
$d1 > $d2;  // false
$d1 < $d2;  // false

$d2->second++;
$d1 != $d2; // true
$d1 < $d2;  // true
$d2 > $d1;  // true
$d1 == $d2; // false
$d1 >= $d2; // false
$d2 <= $d1; // false
```

To determine if an instance is between two other instances you just need two comparisons:

```php
<?php

use ICanBoogie\DateTime;

$now = DateTime::now();

$now > $now->yesterday && $now < $now->tomorrow; // true
```

To determine which instance is the most recent, or the most late, simply use PHP's `min()`
and `max()` functions:

```php
<?php

use ICanBoogie\DateTime;

$now = DateTime::now();
$yesterday = $now->yesterday;
$tomorrow = $now->tomorrow;

$yesterday === min($now, $yesterday, $tomorrow); // true
$tomorrow  === max($now, $yesterday, $tomorrow); // true
```





## DateTime and JSON

Starting with v1.1.0, [DateTime][] instances implements the [JsonSerializable interface][] and
are serialized into ISO-8601 strings.

```php
<?php

use ICanBoogie\DateTime;

$date = new DateTime("2014-10-23 13:50:10", "Europe/Paris");

echo json_encode([ 'date' => $date ]);
// {"date":"2014-10-23T13:50:10+0200"}
```





## Changing multiple properties

The `change()` method is used to change multiple properties at once.

**Note:** Values exceeding ranges are added to their parent values.

```php
<?php

use ICanBoogie\DateTime;

$date = DateTime::now()->change([ 'year' => 2015, 'month' => 5, 'hour' => 12 ]);
```

Using the `$cascade` parameter, setting the hour resets the minute and second to 0, and setting the minute resets the second to 0.

```php
<?php

use ICanBoogie\DateTime;

echo DateTime::from("2015-05-05 12:13:14")->change([ 'hour' => 13 ], true);   // 2015-05-05 13:00:00
```





## Creating a new instance with changed properties

The `with()` method is similar to the `change()` method as it is used to define multiple properties at once, the difference is that the method creates a new instance, leaving the original instance intact.

```php
<?php

use ICanBoogie\DateTime;

$now = DateTime::now();
$next_year = $now->with([ 'year' => $now->year + 1 ]);

spl_object_hash($now) == spl_object_hash($next_year);   // false
```





## Localized formatting

Localized formatting is outside of this package scope, still a _localizer_ can be provided to the
[DateTime][] class to localize its instances, but of course the result depends on the
implementation.

The following example demonstrates how to localize instances using [ICanBoogie/CLDR][] which uses
Unicode's Common Locale Data Repository to format [DateTime][] instances.

```php
<?php

use ICanBoogie\CLDR\Repository;
use ICanBoogie\DateTime;

// …

$repository = new Repository($provider);

DateTime::$localizer = function(DateTime $instance, $locale) use ($repository) {

	return $repository->locales[$locale]->localize($instance);

};

$date = DateTime::from('2015-05-05 23:21:05', 'UTC');

echo $date->localize('fr')->format('long');   // mardi 5 mai 2015 23:13:05 UTC
echo $date->localize('fr')->as_medium;        // 5 mai 2015 23:13:05
```






----------





## Requirement

The package requires PHP 5.4 or later.

Starting with v1.1.0 the package implements the [JsonSerializable interface][]
 and thus requires PHP 5.4+. If you only have access to PHP 5.3, use the branch
 [1.0.x](https://github.com/ICanBoogie/DateTime/tree/1.0) instead.





## Installation

The recommended way to install this package is through [Composer](https://getcomposer.org/):

```
$ composer require icanboogie/datetime
```

The package [icanboogie/common](https://github.com/ICanBoogie/Common) is suggested to
provide finer exceptions, such as [PropertyNotDefined][] and [PropertyNotWritable][].
If the package is not included, `RunTimeException` instances are thrown instead.





### Cloning the repository

The package is [available on GitHub](https://github.com/ICanBoogie/DateTime), its repository can be
cloned with the following command line:

	$ git clone https://github.com/ICanBoogie/DateTime.git





## Documentation

The package is documented as part of the [ICanBoogie](http://icanboogie.org/) framework
[documentation][]. The documentation for the package and its
dependencies can be generated with the `make doc` command. The documentation is generated in
the `build/docs` directory using [ApiGen](http://apigen.org/). The package directory can later by
cleaned with the `make clean` command.

The following classes are documented:

- [DateTime][]
- [TimeZone][]
- [TimeZoneLocation][]





## Testing

The test suite is ran with the `make test` command. [Composer](http://getcomposer.org/) is
automatically installed as well as all the dependencies required to run the suite. The package
directory can later be cleaned with the `make clean` command.

The package is continuously tested by [Travis CI](http://about.travis-ci.org/).

[![Build Status](https://travis-ci.org/ICanBoogie/DateTime.svg)](https://travis-ci.org/ICanBoogie/DateTime)
[![Code Coverage](https://img.shields.io/coveralls/ICanBoogie/DateTime.svg)](https://coveralls.io/r/ICanBoogie/DateTime)





## License

**icanboogie/datetime** is licensed under the New BSD License - See the [LICENSE](LICENSE) file for details.





[ICanBoogie/CLDR]:            https://github.com/ICanBoogie/CLDR
[JsonSerializable interface]: http://php.net/manual/en/class.jsonserializable.php
[documentation]:              http://api.icanboogie.org/datetime/latest/
[DateTime]:                   http://api.icanboogie.org/datetime/latest/class-ICanBoogie.DateTime.html
[TimeZone]:                   http://api.icanboogie.org/datetime/latest/class-ICanBoogie.TimeZone.html)
[TimeZoneLocation]:           http://api.icanboogie.org/datetime/latest/class-ICanBoogie.TimeZoneLocation.html
[PropertyNotDefined]:         http://api.icanboogie.org/common/1.2/class-ICanBoogie.PropertyNotDefined.html
[PropertyNotWritable]:        http://api.icanboogie.org/common/1.2/class-ICanBoogie.PropertyNotWritable.html
