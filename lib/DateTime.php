<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie;

use ICanBoogie\DateTime\AdditionalFormats;

/**
 * Representation of a date and time.
 *
 * <pre>
 * <?php
 *
 * // Let's say that _now_ is 2013-02-03 21:03:45 in Paris
 *
 * use ICanBoogie\DateTime;
 *
 * date_default_timezone_set('EST'); // set local time zone to Eastern Standard Time
 *
 * $time = new DateTime('now', 'Europe/Paris');
 *
 * echo $time;                             // 2013-02-03T21:03:45+0100
 * echo $time->utc;                        // 2013-02-03T20:03:45Z
 * echo $time->local;                      // 2013-02-03T15:03:45-0500
 * echo $time->utc->local;                 // 2013-02-03T15:03:45-0500
 * echo $time->utc->is_utc;                // true
 * echo $time->utc->is_local;              // false
 * echo $time->local->is_utc;              // false
 * echo $time->local->is_local;            // true
 * echo $time->is_dst;                     // false
 *
 * echo $time->as_rss;                     // Sun, 03 Feb 2013 21:03:45 +0100
 * echo $time->as_db;                      // 2013-02-03 21:03:45
 *
 * echo $time->as_time;                    // 21:03:45
 * echo $time->utc->as_time;               // 20:03:45
 * echo $time->local->as_time;             // 15:03:45
 * echo $time->utc->local->as_time;        // 15:03:45
 *
 * echo $time->quarter;                    // 1
 * echo $time->week;                       // 5
 * echo $time->day;                        // 3
 * echo $time->minute;                     // 3
 * echo $time->is_monday;                  // false
 * echo $time->is_saturday;                // true
 * echo $time->is_today;                   // true
 * echo $time->tomorrow;                   // 2013-02-04T00:00:00+0100
 * echo $time->tomorrow->is_future         // true
 * echo $time->yesterday;                  // 2013-02-02T00:00:00+0100
 * echo $time->yesterday->is_past          // true
 * echo $time->monday;                     // 2013-01-28T00:00:00+0100
 * echo $time->sunday;                     // 2013-02-03T00:00:00+0100
 *
 * echo $time->timestamp;                  // 1359921825
 * echo $time;                             // 2013-02-03T21:03:45+0100
 * $time->timestamp += 3600 * 4;
 * echo $time;                             // 2013-02-04T01:03:45+0100
 *
 * echo $time->zone;                       // Europe/Paris
 * echo $time->zone->offset;               // 3600
 * echo $time->zone->location;             // FR,48.86667,2.33333
 * echo $time->zone->location->latitude;   // 48.86667
 * $time->zone = 'Asia/Tokyo';
 * echo $time;                             // 2013-02-04T09:03:45+0900
 *
 * $time->hour += 72;
 * echo "Rendez-vous in 72 hours: $time";  // Rendez-vous in 72 hours: 2013-02-07T05:03:45+0900
 * </pre>
 *
 * Empty dates are also supported:
 *
 * <pre>
 * <?php
 *
 * use ICanBoogie\DateTime;
 *
 * $time = new DateTime('0000-00-00', 'utc');
 * // or
 * $time = DateTime::none();
 *
 * echo $time->is_empty;                   // true
 * echo $time->as_date;                    // 0000-00-00
 * echo $time->as_db;                      // 0000-00-00 00:00:00
 * echo $time;                             // ""
 * </pre>
 *
 * @property-read int $quarter Quarter of the year.
 * @property-read int $week Week of the year.
 * @property-read int $weekday Day of the week.
 * @property-read int $year_day Day of the year.
 *
 * @property-read DateTime $utc A new instance in the UTC timezone.
 * @property-read DateTime $local A new instance in the local timezone.
 *
 * @method DateTime setTimezone(mixed $timezone)
 * @method DateTime change(array $options, $cascade = false)
 *
 * @see http://en.wikipedia.org/wiki/ISO_8601
 */
class DateTime extends \DateTimeImmutable implements \JsonSerializable, AdditionalFormats
{
	use DateTime\Shared;
	use DateTime\Readers;

	/*
	 * The following constants need to be defined because they are only defined by \DateTimeImmutable
	 */
	const ATOM = \DateTime::ATOM;
	const RSS = \DateTime::RSS;
	const ISO8601 = \DateTime::ISO8601;
	const RFC822 = \DateTime::RFC822;
	const RFC850 = \DateTime::RFC850;
	const RFC1036 = \DateTime::RFC1036;
	const RFC1123 = \DateTime::RFC1123;
	const RFC2822 = \DateTime::RFC2822;
	const RFC3339 = \DateTime::RFC3339;
	const W3C = \DateTime::W3C;

	/**
	 * We redefine the constant to make sure that the cookie uses a valid pattern.
	 *
	 * @see http://grokbase.com/t/php/php-bugs/111xynxd6m/php-bug-bug-53879-new-datetime-createfromformat-fails-to-parse-cookie-expiration-date
	 *
	 * @var string
	 */
	const COOKIE = 'l, d-M-Y H:i:s T';

	/**
	 * Callable used to create localized instances.
	 *
	 * @var callable
	 */
	static public $localizer = null;

	/**
	 * Returns an instance with the current local time and the local time zone.
	 *
	 * **Note:** Subsequent calls return equal times, event if they are minutes apart. _now_
	 * actually refers to the `REQUEST_TIME` or, if it is now available, to the first time
	 * the method was invoked.
	 *
	 * @return static
	 */
	static public function now()
	{
		static $now;

		if (!$now)
		{
			$now = empty($_SERVER['REQUEST_TIME']) ? new static : (new static('@' . $_SERVER['REQUEST_TIME']))->local;
		}

		return $now;
	}

	/**
	 * @inheritdoc
	 *
	 * @throws \LogicException in attempt to set a property.
	 */
	public function __set($property, $value)
	{
		throw new \LogicException("Property is not writable: $property.");
	}

	/**
	 * Instantiate a new instance with changes properties.
	 *
	 * @param array $options
	 * @param bool $cascade
	 *
	 * @return DateTime
	 */
	public function with(array $options, $cascade = false)
	{
		return $this->change($options, $cascade);
	}
}
