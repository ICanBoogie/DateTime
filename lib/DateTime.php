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
 * $time = new DateTime('0000-00-00', 'utc');
 * // or
 * $time = DateTime::none();
 * echo $time;                             // -0001-11-30T00:00:00Z
 * echo $time->is_empty;                   // true
 * echo $time->as_date;                    // 0000-00-00
 * echo $time->as_db;                      // 0000-00-00 00:00:00
 *
 * </pre>
 *
 * @property int $timestamp Unix timestamp.
 * @property int $day Day of the month.
 * @property int $hour Hour of the day.
 * @property int $minute Minute of the hour.
 * @property int $month Month of the year.
 * @property-read int $quarter Quarter of the year.
 * @property int $second Second of the minute.
 * @property-read int $week Week of the year.
 * @property-read int $weekday Day of the week.
 * @property int $year Year.
 * @property-read int $year_day Day of the year.
 * @property-read bool $is_monday `true` if the instance represents Monday.
 * @property-read bool $is_tuesday `true` if the instance represents Tuesday.
 * @property-read bool $is_wednesday `true` if the instance represents Wednesday.
 * @property-read bool $is_thursday `true` if the instance represents Thursday.
 * @property-read bool $is_friday `true` if the instance represents Friday.
 * @property-read bool $is_saturday `true` if the instance represents Saturday.
 * @property-read bool $is_sunday `true` if the instance represents Sunday.
 * @property-read bool $is_today `true` if the instance is today.
 * @property-read bool $is_past `true` if the instance lies in the past.
 * @property-read bool $is_future `true` if the instance lies in the future.
 * @property-read bool $is_empty `true` if the instance represents an empty date such as "0000-00-00" or "0000-00-00 00:00:00".
 * @property-read DateTime $tomorrow A new instance representing the next day. Time is reset to 00:00:00.
 * @property-read DateTime $yesterday A new instance representing the previous day. Time is reset to 00:00:00.
 * @property-read DateTime $monday A new instance representing Monday of the week. Time is reset to 00:00:00.
 * @property-read DateTime $tuesday A new instance representing Tuesday of the week. Time is reset to 00:00:00.
 * @property-read DateTime $wednesday A new instance representing Wednesday of the week. Time is reset to 00:00:00.
 * @property-read DateTime $thursday A new instance representing Thursday of the week. Time is reset to 00:00:00.
 * @property-read DateTime $friday A new instance representing Friday of the week. Time is reset to 00:00:00.
 * @property-read DateTime $saturday A new instance representing Saturday of the week. Time is reset to 00:00:00.
 * @property-read DateTime $sunday A new instance representing Sunday of the week. Time is reset to 00:00:00.
 *
 * @property-read string $as_atom The instance formatted according to {@link ATOM}.
 * @property-read string $as_cookie The instance formatted according to {@link COOKIE}.
 * @property-read string $as_iso8601 The instance formatted according to {@link ISO8601}.
 * @property-read string $as_rfc822 The instance formatted according to {@link RFC822}.
 * @property-read string $as_rfc850 The instance formatted according to {@link RFC850}.
 * @property-read string $as_rfc1036 The instance formatted according to {@link RFC1036}.
 * @property-read string $as_rfc1123 The instance formatted according to {@link RFC1123}.
 * @property-read string $as_rfc2822 The instance formatted according to {@link RFC2822}.
 * @property-read string $as_rfc3339 The instance formatted according to {@link RFC3339}.
 * @property-read string $as_rss The instance formatted according to {@link RSS}.
 * @property-read string $as_w3c The instance formatted according to {@link W3C}.
 * @property-read string $as_db The instance formatted according to {@link DB}.
 * @property-read string $as_number The instance formatted according to {@link NUMBER}.
 * @property-read string $as_date The instance formatted according to {@link DATE}.
 * @property-read string $as_time The instance formatted according to {@link TIME}.
 *
 * @property TimeZone $zone The timezone of the instance.
 * @property-read DateTime $utc A new instance in the UTC timezone.
 * @property-read DateTime $local A new instance in the local timezone.
 * @property-read bool $is_utc `true` if the instance is in the UTC timezone.
 * @property-read bool $is_local `true` if the instance is in the local timezone.
 * @property-read bool $is_dst `true` if time occurs during Daylight Saving Time in its time zone.
 *
 * @method string format_as_atom() format_as_atom() Formats the instance according to {@link ATOM}.
 * @method string format_as_cookie() format_as_cookie() Formats the instance according to {@link COOKIE}.
 * @method string format_as_iso8601() format_as_iso8601() Formats the instance according to {@link ISO8601}.
 * @method string format_as_rfc822() format_as_rfc822() Formats the instance according to {@link RFC822}.
 * @method string format_as_rfc850() format_as_rfc850() Formats the instance according to {@link RFC850}.
 * @method string format_as_rfc1036() format_as_rfc1036() Formats the instance according to {@link RFC1036}.
 * @method string format_as_rfc1123() format_as_rfc1123() Formats the instance according to {@link RFC1123}.
 * @method string format_as_rfc2822() format_as_rfc2822() Formats the instance according to {@link RFC2822}.
 * @method string format_as_rfc3339() format_as_rfc3339() Formats the instance according to {@link RFC3339}.
 * @method string format_as_rss() format_as_rss() Formats the instance according to {@link RSS}.
 * @method string format_as_w3c() format_as_w3c() Formats the instance according to {@link W3C}.
 * @method string format_as_db() format_as_db() Formats the instance according to {@link DB}.
 * @method string format_as_number() format_as_number() Formats the instance according to {@link NUMBER}.
 * @method string format_as_date() format_as_date() Formats the instance according to {@link DATE}.
 * @method string format_as_time() format_as_time() Formats the instance according to {@link TIME}.
 *
 * @see http://en.wikipedia.org/wiki/ISO_8601
 */
class DateTime extends \DateTime implements \JsonSerializable
{
	/**
	 * We redefine the constant to make sure that the cookie uses a valid pattern.
	 *
	 * @see http://grokbase.com/t/php/php-bugs/111xynxd6m/php-bug-bug-53879-new-datetime-createfromformat-fails-to-parse-cookie-expiration-date
	 *
	 * @var string
	 */
	const COOKIE = 'l, d-M-Y H:i:s T';

	/**
	 * DB (example: 2013-02-03 20:59:03)
	 *
	 * @var string
	 */
	const DB = 'Y-m-d H:i:s';

	/**
	 * Number (example: 20130203205903)
	 *
	 * @var string
	 */
	const NUMBER = 'YmdHis';

	/**
	 * Date (example: 2013-02-03)
	 *
	 * @var string
	 */
	const DATE = 'Y-m-d';

	/**
	 * Time (example: 20:59:03)
	 *
	 * @var string
	 */
	const TIME = 'H:i:s';

	/**
	 * Creates a {@link DateTime} instance from a source.
	 *
	 * <pre>
	 * <?php
	 *
	 * use ICanBoogie\DateTime;
	 *
	 * DateTime::from(new \DateTime('2001-01-01 01:01:01', new \DateTimeZone('Europe/Paris')));
	 * DateTime::from('2001-01-01 01:01:01', 'Europe/Paris');
	 * DateTime::from('now');
	 * </pre>
	 *
	 * @param mixed $source
	 * @param mixed $timezone The time zone to use to create the time. The value is ignored if the
	 * source is an instance of {@link \DateTime}.
	 *
	 * @return DateTime
	 */
	static public function from($source, $timezone=null)
	{
		if ($source instanceof static)
		{
			return clone $source;
		}
		else if ($source instanceof \DateTime)
		{
			return new static($source->format(self::DB), $source->getTimezone());
		}

		return new static($source, $timezone);
	}

	/**
	 * Returns an instance with the current local time and the local time zone.
	 *
	 * @return DateTime
	 */
	static public function now()
	{
		return new static();
	}

	/**
	 * Returns an instance representing an empty date ("0000-00-00").
	 *
	 * <pre>
	 * <?php
	 *
	 * use ICanBoogie\DateTime;
	 *
	 * $d = DateTime::none();
	 * $d->is_empty;                      // true
	 * $d->zone->name;                    // "UTC"
	 *
	 * $d = DateTime::none('Asia/Tokyo');
	 * $d->is_empty;                      // true
	 * $d->zone->name;                    // "Asia/Tokio"
	 * </pre>
	 *
	 * @param \DateTimeZone|string $timezone The time zone in which the empty date is created.
	 * Defaults to "UTC".
	 *
	 * @return DateTime
	 */
	static public function none($timezone='utc')
	{
		return new static('0000-00-00', $timezone);
	}

	/**
	 * If the time zone is specified as a string a {@link \DateTimeZone} instance is created and
	 * used instead.
	 *
	 * <pre>
	 * <?php
	 *
	 * use ICanBoogie\DateTime;
	 *
	 * new DateTime('2001-01-01 01:01:01', new \DateTimeZone('Europe/Paris')));
	 * new DateTime('2001-01-01 01:01:01', 'Europe/Paris');
	 * new DateTime;
	 * </pre>
	 *
	 * @param string $time Defaults to "now".
	 * @param \DateTimeZone|string|null $timezone
	 */
	public function __construct($time='now', $timezone=null)
	{
		if (is_string($timezone))
		{
			$timezone = new \DateTimeZone($timezone);
		}

		#
		# PHP 5.3.3 considers null $timezone as an error and will complain that it is not
		# a \DateTimeZone instance.
		#

		$timezone === null ? parent::__construct($time) : parent::__construct($time, $timezone);
	}

	public function __get($property)
	{
		if (strpos($property, 'as_') === 0)
		{
			return call_user_func(array($this, 'format_' . $property));
		}

		switch ($property)
		{
			case 'timestamp':
				return $this->getTimestamp();

			case 'year':
				return (int) $this->format('Y');
			case 'quarter':
				return floor(($this->month - 1) / 3) + 1;
			case 'month':
				return (int) $this->format('m');
			case 'week':
				return (int) $this->format('W');
			case 'year_day':
				return (int) $this->format('z') + 1;
			case 'weekday':
				return (int) $this->format('w') ?: 7;
			case 'day':
				return (int) $this->format('d');
			case 'hour':
				return (int) $this->format('H');
			case 'minute':
				return (int) $this->format('i');
			case 'second':
				return (int) $this->format('s');
			case 'is_monday':
				return $this->weekday == 1;
			case 'is_tuesday':
				return $this->weekday == 2;
			case 'is_wednesday':
				return $this->weekday == 3;
			case 'is_thursday':
				return $this->weekday == 4;
			case 'is_friday':
				return $this->weekday == 5;
			case 'is_saturday':
				return $this->weekday == 6;
			case 'is_sunday':
				return $this->weekday == 7;
			case 'is_today':
				$now = new static('now', $this->zone);
				return $this->as_date === $now->as_date;
			case 'is_past':
				return $this < new static('now', $this->zone);
			case 'is_future':
				return $this > new static('now', $this->zone);
			case 'is_empty':
				return $this->year == -1 && $this->month == 11 && $this->day == 30;
			case 'tomorrow':
				$time = clone $this;
				$time->modify('+1 day');
				$time->setTime(0, 0, 0);
				return $time;
			case 'yesterday':
				$time = clone $this;
				$time->modify('-1 day');
				$time->setTime(0, 0, 0);
				return $time;

			/*
			 * days
			 */
			case 'monday':
			case 'tuesday':
			case 'wednesday':
			case 'thursday':
			case 'friday':
			case 'saturday':
			case 'sunday':

				return $this->{ 'get_' . $property }();

			case 'zone':
				return TimeZone::from($this->getTimezone());
			case 'utc':
			case 'local':
				$time = clone $this;
				$time->setTimezone($property);
				return $time;
			case 'is_utc':
				return $this->zone->name == 'UTC';
			case 'is_local':
				return $this->zone->name == date_default_timezone_get();
			case 'is_dst':
				$timestamp = $this->timestamp;
				$transitions = $this->zone->getTransitions($timestamp, $timestamp);
				return $transitions[0]['isdst'];
		}

		if (class_exists('ICanBoogie\PropertyNotDefined'))
		{
			throw new PropertyNotDefined(array($property, $this));
		}
		else
		{
			throw new \RuntimeException("Property is not defined: $property.");
		}
	}

	/**
	 * Returns Monday of the week.
	 *
	 * @return DateTime
	 */
	protected function get_monday()
	{
		$time = clone $this;
		$day = $time->weekday;

		if ($day != 1)
		{
			$time->modify('-' . ($day - 1) . ' day');
		}

		$time->setTime(0, 0, 0);

		return $time;
	}

	/**
	 * Returns Tuesday of the week.
	 *
	 * @return DateTime
	 */
	protected function get_tuesday()
	{
		return $this->monday->modify('+1 day');
	}

	/**
	 * Returns Wednesday of the week.
	 *
	 * @return DateTime
	 */
	protected function get_wednesday()
	{
		return $this->monday->modify('+2 day');
	}

	/**
	 * Returns Thursday of the week.
	 *
	 * @return DateTime
	 */
	protected function get_thursday()
	{
		return $this->monday->modify('+3 day');
	}

	/**
	 * Returns Friday of the week.
	 *
	 * @return DateTime
	 */
	protected function get_friday()
	{
		return $this->monday->modify('+4 day');
	}

	/**
	 * Returns Saturday of the week.
	 *
	 * @return DateTime
	 */
	protected function get_saturday()
	{
		return $this->monday->modify('+5 day');
	}

	/**
	 * Returns Sunday of the week.
	 *
	 * @return DateTime
	 */
	protected function get_sunday()
	{
		$time = clone $this;
		$day = $time->weekday;

		if ($day != 7)
		{
			$time->modify('+' . (7 - $day) . ' day');
		}

		$time->setTime(0, 0, 0);

		return $time;
	}

	/**
	 * Sets the {@link $year}, {@link $month}, {@link $day}, {@link $hour}, {@link $minute},
	 * {@link $second}, {@link $timestamp} and {@link $zone} properties.
	 *
	 * @throws PropertyNotWritable in attempt to set a read-only property.
	 * @throws PropertyNotDefined in attempt to set an unsupported property.
	 *
	 * @inheritdoc
	 */
	public function __set($property, $value)
	{
		static $readonly = array('quarter', 'week', 'year_day', 'weekday',
		'tomorrow', 'yesterday', 'utc', 'local');

		switch ($property)
		{
			case 'year':
			case 'month':
			case 'day':
			case 'hour':
			case 'minute':
			case 'second':
				$this->change(array($property => $value));
				return;

			case 'timestamp':
				$this->setTimestamp($value);
				return;

			case 'zone':
				$this->setTimezone($value);
				return;
		}

		if (strpos($property, 'is_') === 0 || strpos($property, 'as_') === 0 || in_array($property, $readonly) || method_exists($this, 'get_' . $property))
		{
			if (class_exists('ICanBoogie\PropertyNotWritable'))
			{
				throw new PropertyNotWritable(array($property, $this));
			}
			else
			{
				throw new \RuntimeException("Property is not writeable: $property.");
			}
		}

		if (class_exists('ICanBoogie\PropertyNotDefined'))
		{
			throw new PropertyNotDefined(array($property, $this));
		}
		else
		{
			throw new \RuntimeException("Property is not defined: $property.");
		}
	}

	/**
	 * Handles the `format_as_*` methods.
	 *
	 * If the format is {@link RFC822} or {@link RFC1123} and the time zone is equivalent to GMT,
	 * the offset `+0000` is replaced by `GMT` according to the specs.
	 *
	 * If the format is {@link ISO8601} and the time zone is equivalent to UTC, the offset `+0000`
	 * is replaced by `Z` according to the specs.
	 *
	 * @throws \BadMethodCallException in attempt to call an unsupported method.
	 *
	 * @inheritdoc
	 */
	public function __call($method, $arguments)
	{
		if (strpos($method, 'format_as_') === 0)
		{
			$as = strtoupper(substr($method, strlen('format_as_')));
			$format = constant(__CLASS__ . '::' . $as);
			$value = $this->format($format);

			if ($as == 'RFC822' || $as == 'RFC1123')
			{
				$value = str_replace('+0000', 'GMT', $value);
			}
			else if ($as == 'ISO8601')
			{
				$value = str_replace('+0000', 'Z', $value);
			}

			return $value;
		}

		throw new \BadMethodCallException("Unsupported method: $method.");
	}

	/**
	 * Returns the datetime formatted as {@link ISO8601}.
	 *
	 * @return string The instance rendered as an {@link ISO8601} string, or an empty string if the
	 * datetime is empty.
	 */
	public function __toString()
	{
		return $this->is_empty ? "" : $this->as_iso8601;
	}

	/**
	 * Returns a {@link ISO8601} representation of the instance.
	 *
	 * @return string
	 */
	public function jsonSerialize()
	{
		return (string) $this;
	}

	/**
	 * The timezone can be specified as a string.
	 *
	 * If the timezone is `local` the timezone returned by {@link date_default_timezone_get()} is
	 * used instead.
	 *
	 * @inheritdoc
	 */
	public function setTimezone($timezone)
	{
		if ($timezone === 'local')
		{
			$timezone = date_default_timezone_get();
		}

		if (!($timezone instanceof \DateTimeZone))
		{
			$timezone = new \DateTimeZone($timezone);
		}

		return parent::setTimezone($timezone);
	}

	/**
	 * Modifies the properties of the instance according to the options.
	 *
	 * The following properties can be updated: {@link $year}, {@link $month}, {@link $day},
	 * {@link $hour}, {@link $minute} and {@link $second}.
	 *
	 * Note: Values exceeding ranges are added to their parent values.
	 *
	 * <pre>
	 * <?php
	 *
	 * use ICanBoogie\DateTime;
	 *
	 * $time = new DateTime('now');
	 * $time->change(array('year' => 2000, 'second' => 0));
	 * </pre>
	 *
	 * @param array $options
	 * @param bool $cascade If `true`, time options (`hour`, `minute`, `second`) reset
	 * cascading, so if only the hour is passed, then minute and second is set to 0. If the hour
	 * and minute is passed, then second is set to 0.
	 *
	 * @return DateTime
	 */
	public function change(array $options, $cascade=false)
	{
		static $default_options = array
		(
			'year' => null,
			'month' => null,
			'day' => null,
			'hour' => null,
			'minute' => null,
			'second' => null
		);

		$options = array_intersect_key($options + $default_options, $default_options);

		$year = null;
		$month = null;
		$day = null;
		$hour = null;
		$minute = null;
		$second = null;

		extract($options);

		if ($cascade)
		{
			if ($hour !== null && $minute === null)
			{
				$minute = 0;
			}

			if ($minute !== null && $second === null)
			{
				$second = 0;
			}
		}

		if ($year !== null || $month !== null || $day !== null)
		{
			$this->setDate
			(
				$year === null ? $this->year : $year,
				$month === null ? $this->month : $month,
				$day === null ? $this->day : $day
			);
		}

		if ($hour !== null || $minute !== null || $second !== null)
		{
			$this->setTime
			(
				$hour === null ? $this->hour : $hour,
				$minute === null ? $this->minute : $minute,
				$second === null ? $this->second : $second
			);
		}

		return $this;
	}

	/**
	 * If the instance represents an empty date and the format is {@link DATE} or {@link DB},
	 * an empty date is returned, respectively "0000-00-00" and "0000-00-00 00:00:00". Note that
	 * the time information is discarded for {@link DB}. This only apply to {@link DATE} and
	 * {@link DB} formats. For instance {@link RSS} will return the following string:
	 * "Wed, 30 Nov -0001 00:00:00 +0000".
	 *
	 * @inheritdoc
	 */
	public function format($format)
	{
		if (($format == self::DATE || $format == self::DB) && $this->is_empty)
		{
			return $format == self::DATE ? '0000-00-00' : '0000-00-00 00:00:00';
		}

		return parent::format($format);
	}
}
