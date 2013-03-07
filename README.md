# DateTime [![Build Status](https://travis-ci.org/ICanBoogie/DateTime.png?branch=master)](https://travis-ci.org/ICanBoogie/DateTime)

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

$time = new DateTime('0000-00-00', 'utc');
// or
$time = DateTime::none();
echo $time;                             // -0001-11-30T00:00:00Z
echo $time->is_empty;                   // true
echo $time->as_date;                    // 0000-00-00
echo $time->as_db;                      // 0000-00-00 00:00:00
```





### Acknowledgements

The implementation of the [DateTime](http://icanboogie.org/docs/class-ICanBoogie.DateTime.html)
class is vastly inspired by Ruby's [Time](http://www.ruby-doc.org/core-1.9.3/Time.html) class.





## Requirement

The package requires PHP 5.3 or later.





## Installation

The recommended way to install this package is through [composer](http://getcomposer.org/).
Create a `composer.json` file and run `php composer.phar install` command to install it:

```json
{
	"minimum-stability": "dev",
	"require":
	{
		"icanboogie/datetime": "*"
	}
}
```

The package [icanboogie/common](https://packagist.org/packages/icanboogie/common) is suggested to
provide finer exceptions, such as [PropertyNotDefined](http://icanboogie.org/docs/class-ICanBoogie.PropertyNotDefined.html)
and [PropertyNotWriteable](http://icanboogie.org/docs/class-ICanBoogie.PropertyNotWritable.html).
If the package is not included, `RunTimeException` instances are thrown instead.





### Cloning the repository

The package is [available on GitHub](https://github.com/ICanBoogie/DateTime), its repository can be
cloned with the following command line:

	$ git clone git://github.com/ICanBoogie/DateTime.git





## Documentation

The package is documented as part of the [ICanBoogie](http://icanboogie.org/) framework
[documentation](http://icanboogie.org/docs/). The documentation for the package and its
dependencies can be generated with the `make doc` command. The documentation is generated in
the `docs` directory using [ApiGen](http://apigen.org/). The package directory can later by
cleaned with the `make clean` command.

The following classes are documented: 

- [DateTime](http://icanboogie.org/docs/class-ICanBoogie.DateTime.html)
- [TimeZone](http://icanboogie.org/docs/class-ICanBoogie.TimeZone.html)
- [TimeZoneLocation](http://icanboogie.org/docs/class-ICanBoogie.TimeZoneLocation.html)





## Testing

The test suite is ran with the `make test` command. [Composer](http://getcomposer.org/) is
automatically installed as well as all the dependencies required to run the suite. The package
directory can later be cleaned with the `make clean` command.

The package is continuously tested by [Travis CI](http://about.travis-ci.org/).

[![Build Status](https://travis-ci.org/ICanBoogie/DateTime.png?branch=master)](https://travis-ci.org/ICanBoogie/DateTime)





## License

ICanBoogie/DateTime is licensed under the New BSD License - See the [LICENSE](https://raw.github.com/ICanBoogie/DateTime/master/LICENSE) file for details.