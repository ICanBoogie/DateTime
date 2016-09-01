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

/**
 * Representation of a date and time.
 *
 * <pre>
 * <?php
 *
 * // Let's say that _now_ is 2013-02-03 21:03:45 in Paris
 *
 * use ICanBoogie\ImmutableDateTime;
 *
 * date_default_timezone_set('EST'); // set local time zone to Eastern Standard Time
 *
 * $time = new ImmutableDateTime('now', 'Europe/Paris');
 *
 * echo $time;                             // 2013-02-03T21:03:45+01:00
 * echo $time->utc;                        // 2013-02-03T20:03:45Z
 * echo $time->local;                      // 2013-02-03T15:03:45-05:00
 * echo $time->utc->local;                 // 2013-02-03T15:03:45-05:00
 * echo $time->utc->is_utc;                // true
 * echo $time->utc->is_local;              // false
 * echo $time->local->is_utc;              // false
 * echo $time->local->is_local;            // true
 * echo $time->is_dst;                     // false
 *
 * echo $time->as_rss;                     // Sun, 03 Feb 2013 21:03:45 +01:00
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
 * echo $time->tomorrow;                   // 2013-02-04T00:00:00+01:00
 * echo $time->tomorrow->is_future         // true
 * echo $time->yesterday;                  // 2013-02-02T00:00:00+01:00
 * echo $time->yesterday->is_past          // true
 * echo $time->monday;                     // 2013-01-28T00:00:00+01:00
 * echo $time->sunday;                     // 2013-02-03T00:00:00+01:00
 *
 * echo $time->timestamp;                  // 1359921825
 * echo $time;                             // 2013-02-03T21:03:45+01:00
 * $time->timestamp += 3600 * 4;
 * echo $time;                             // 2013-02-04T01:03:45+01:00
 *
 * echo $time->timezone;                   // Europe/Paris
 * // or
 * echo $time->tz;                         // Europe/Paris
 * echo $time->tz->offset;                 // 3600
 * echo $time->tz->location;               // FR,48.86667,2.33333
 * echo $time->tz->location->latitude;     // 48.86667
 * </pre>
 *
 * Empty dates are also supported:
 *
 * <pre>
 * <?php
 *
 * use ICanBoogie\ImmutableDateTime as ImmutableDateTime;
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
 * @property-read ImmutableDateTime $tomorrow A new instance representing the next day. Time is reset to 00:00:00.
 * @property-read ImmutableDateTime $yesterday A new instance representing the previous day. Time is reset to 00:00:00.
 * @property-read ImmutableDateTime $monday A new instance representing Monday of the week. Time is reset to 00:00:00.
 * @property-read ImmutableDateTime $tuesday A new instance representing Tuesday of the week. Time is reset to 00:00:00.
 * @property-read ImmutableDateTime $wednesday A new instance representing Wednesday of the week. Time is reset to 00:00:00.
 * @property-read ImmutableDateTime $thursday A new instance representing Thursday of the week. Time is reset to 00:00:00.
 * @property-read ImmutableDateTime $friday A new instance representing Friday of the week. Time is reset to 00:00:00.
 * @property-read ImmutableDateTime $saturday A new instance representing Saturday of the week. Time is reset to 00:00:00.
 * @property-read ImmutableDateTime $sunday A new instance representing Sunday of the week. Time is reset to 00:00:00.
 *
 * @property-read ImmutableDateTime $utc A new instance in the UTC timezone.
 * @property-read ImmutableDateTime $local A new instance in the local timezone.
 *
 * @method ImmutableDateTime setTimezone(mixed $timezone)
 * @method ImmutableDateTime change(array $options, $cascade = false)
 *
 * @see http://en.wikipedia.org/wiki/ISO_8601
 */
class ImmutableDateTime extends \DateTimeImmutable implements \JsonSerializable, DateTime
{
	use DateTime\Shared;
	use DateTime\Readers;

	/*
	 * The following constants need to be defined because they are only defined by \DateTime
	 */
	const ATOM    = MutableDateTime::ATOM;
	const COOKIE  = MutableDateTime::COOKIE;
	const RSS     = MutableDateTime::RSS;
	const ISO8601 = MutableDateTime::ISO8601;
	const RFC822  = MutableDateTime::RFC822;
	const RFC850  = MutableDateTime::RFC850;
	const RFC1036 = MutableDateTime::RFC1036;
	const RFC1123 = MutableDateTime::RFC1123;
	const RFC2822 = MutableDateTime::RFC2822;
	const RFC3339 = MutableDateTime::RFC3339;
	const W3C     = MutableDateTime::W3C;

	/**
	 * @inheritdoc
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
	 * @inheritdoc
	 */
	public function with(array $options, $cascade = false)
	{
		return $this->change($options, $cascade);
	}
}
