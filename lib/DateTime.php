<?php

namespace ICanBoogie;

use DateTimeZone;

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
 * @property-read string $as_atom The instance formatted according to {@see ATOM}.
 * @property-read string $as_cookie The instance formatted according to {@see COOKIE}.
 * @property-read string $as_iso8601 The instance formatted according to {@see ISO8601}.
 * @property-read string $as_rfc822 The instance formatted according to {@see RFC822}.
 * @property-read string $as_rfc850 The instance formatted according to {@see RFC850}.
 * @property-read string $as_rfc1036 The instance formatted according to {@see RFC1036}.
 * @property-read string $as_rfc1123 The instance formatted according to {@see RFC1123}.
 * @property-read string $as_rfc2822 The instance formatted according to {@see RFC2822}.
 * @property-read string $as_rfc3339 The instance formatted according to {@see RFC3339}.
 * @property-read string $as_rss The instance formatted according to {@see RSS}.
 * @property-read string $as_w3c The instance formatted according to {@see W3C}.
 * @property-read string $as_db The instance formatted according to {@see DB}.
 * @property-read string $as_number The instance formatted according to {@see NUMBER}.
 * @property-read string $as_date The instance formatted according to {@see DATE}.
 * @property-read string $as_time The instance formatted according to {@see TIME}.
 *
 * @property TimeZone $zone The timezone of the instance.
 * @property-read DateTime $utc A new instance in the UTC timezone.
 * @property-read DateTime $local A new instance in the local timezone.
 * @property-read bool $is_utc `true` if the instance is in the UTC timezone.
 * @property-read bool $is_local `true` if the instance is in the local timezone.
 * @property-read bool $is_dst `true` if time occurs during Daylight Saving Time in its time zone.
 *
 * @method string format_as_atom() Formats the instance according to {@see ATOM}.
 * @method string format_as_cookie() Formats the instance according to {@see COOKIE}.
 * @method string format_as_iso8601() Formats the instance according to {@see ISO8601}.
 * @method string format_as_rfc822() Formats the instance according to {@see RFC822}.
 * @method string format_as_rfc850() Formats the instance according to {@see RFC850}.
 * @method string format_as_rfc1036() Formats the instance according to {@see RFC1036}.
 * @method string format_as_rfc1123() Formats the instance according to {@see RFC1123}.
 * @method string format_as_rfc2822() Formats the instance according to {@see RFC2822}.
 * @method string format_as_rfc3339() Formats the instance according to {@see RFC3339}.
 * @method string format_as_rss() Formats the instance according to {@see RSS}.
 * @method string format_as_w3c() Formats the instance according to {@see W3C}.
 * @method string format_as_db() Formats the instance according to {@see DB}.
 * @method string format_as_number() Formats the instance according to {@see NUMBER}.
 * @method string format_as_date() Formats the instance according to {@see DATE}.
 * @method string format_as_time() Formats the instance according to {@see TIME}.
 *
 * @link http://en.wikipedia.org/wiki/ISO_8601
 */
class DateTime extends \DateTime implements \JsonSerializable
{
	/**
	 * DB (example: 2013-02-03 20:59:03)
	 */
	public const DB = 'Y-m-d H:i:s';

	/**
	 * Number (example: 20130203205903)
	 */
	public const NUMBER = 'YmdHis';

	/**
	 * Date (example: 2013-02-03)
	 */
	public const DATE = 'Y-m-d';

	/**
	 * Time (example: 20:59:03)
	 */
	public const TIME = 'H:i:s';

	/**
	 * Callable used to create localized instances.
	 *
	 * @var callable|null
	 */
	static public $localizer = null;

	/**
	 * Creates a {@see DateTime} instance from a source.
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
	 * @param DateTimeZone|string|null $timezone The time zone to use to create the time.
	 * The value is ignored if the source is an instance of {@see \DateTime}.
	 *
	 * @throws \DateInvalidTimeZoneException
	 * @throws \DateMalformedStringException
	 */
	static public function from(
		self|\DateTimeInterface|string $source,
		DateTimeZone|string|null $timezone = null
	): static
	{
		if ($source instanceof static)
		{
			return clone $source;
		}

		if ($source instanceof \DateTimeInterface)
		{
			return new static($source->format('Y-m-d\TH:i:s.u'), $source->getTimezone());
		}

		return new static($source, $timezone);
	}

	/**
	 * Returns an instance with the current local time and the local time zone.
	 *
	 * **Note:** Subsequent calls return equal times, event if they're minutes apart. _now_
	 * actually refers to the `REQUEST_TIME` or, if it is now available, to the first time
	 * the method was invoked.
	 *
	 * @throws \DateInvalidTimeZoneException
	 * @throws \DateMalformedStringException
	 */
	static public function now(): static
	{
		static $now;

		if (!$now)
		{
			/** @var int|null $time */
			$time = $_SERVER['REQUEST_TIME'] ?? null;

			$now = $time
				? (new static("@$time"))->local
				: new static();
		}

		return clone $now;
	}

	/**
	 * Returns an instance with the current local time and the local time zone.
	 *
	 * **Note:** Subsequent calls may return different times.
	 */
	static public function right_now(): static
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
	 * @param DateTimeZone|string $timezone The time zone in which the empty date is created.
	 * Defaults to "UTC".
	 *
	 * @throws \DateInvalidTimeZoneException
	 * @throws \DateMalformedStringException
	 */
	static public function none(DateTimeZone|string $timezone = 'utc'): static
	{
		return new static('0000-00-00', $timezone);
	}

	/**
	 * If the time zone is specified as a string a {@see \DateTimeZone} instance is created and
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
	 * @throws \DateInvalidTimeZoneException
	 * @throws \DateMalformedStringException
	 */
	public function __construct(string $time = 'now', DateTimeZone|string|null $timezone = null)
	{
		if (is_string($timezone))
		{
			$timezone = new DateTimeZone($timezone);
		}

		parent::__construct($time, $timezone);
	}

	public function __get(string $property): mixed
	{
		if (str_starts_with($property, 'as_'))
		{
			return $this->{ 'format_' . $property }();
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

			/**
			 * days
			 *
			 * @uses get_monday
			 * @uses get_tuesday
			 * @uses get_wednesday
			 * @uses get_thursday
			 * @uses get_friday
			 * @uses get_saturday
			 * @uses get_sunday
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

		throw new \LogicException(
			sprintf("Undefined property: %s::%s", $this::class, $property)
		);
	}

	/**
	 * Returns Monday of the week.
	 *
	 * @throws \DateMalformedStringException
	 */
	private function get_monday(): self
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
	 */
	private function get_tuesday(): self
	{
		return $this->monday->modify('+1 day');
	}

	/**
	 * Returns Wednesday of the week.
	 */
	private function get_wednesday(): self
	{
		return $this->monday->modify('+2 day');
	}

	/**
	 * Returns Thursday of the week.
	 */
	private function get_thursday(): self
	{
		return $this->monday->modify('+3 day');
	}

	/**
	 * Returns Friday of the week.
	 */
	private function get_friday(): self
	{
		return $this->monday->modify('+4 day');
	}

	/**
	 * Returns Saturday of the week.
	 */
	private function get_saturday(): self
	{
		return $this->monday->modify('+5 day');
	}

	/**
	 * Returns Sunday of the week.
	 */
	private function get_sunday(): self
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

	private const READONLY_PROPERTIES = [
		'quarter', 'week', 'year_day', 'weekday',
		'tomorrow', 'yesterday', 'utc', 'local'
	];

	/**
	 * Sets the {@see $year}, {@see $month}, {@see $day}, {@see $hour}, {@see $minute},
	 * {@see $second}, {@see $timestamp} and {@see $zone} properties.
	 *
	 * @throws \DateInvalidTimeZoneException
	 */
	public function __set(string $property, mixed $value): void
	{
		switch ($property)
		{
			case 'year':
			case 'month':
			case 'day':
			case 'hour':
			case 'minute':
			case 'second':
				/** @phpstan-ignore-next-line */
				$this->change([ $property => $value ]);
				return;

			case 'timestamp':
				/** @phpstan-ignore-next-line */
				$this->setTimestamp($value);
				return;

			case 'zone':
				/** @phpstan-ignore-next-line */
				$this->setTimezone($value);
				return;
		}

		if (str_starts_with($property, 'is_')
			|| str_starts_with($property, 'as_')
			|| in_array($property, self::READONLY_PROPERTIES)
			|| method_exists($this, 'get_' . $property)
		)
		{
			throw new \LogicException(
				sprintf("Readonly property: %s::%s", $this::class, $property)
			);
		}

		throw new \LogicException(
			sprintf("Undefined property: %s::%s", $this::class, $property),
		);
	}

	/**
	 * Handles the `format_as_*` methods.
	 *
	 * If the format is {@see RFC822} or {@see RFC1123} and the time zone is equivalent to GMT,
	 * the offset `+0000` is replaced by `GMT` according to the specs.
	 *
	 * If the format is {@see ISO8601} and the time zone is equivalent to UTC, the offset `+0000`
	 * is replaced by `Z` according to the specs.
	 *
	 * @throws \BadMethodCallException in attempt to call an unsupported method.
	 *
	 * @phpstan-ignore-next-line
	 */
	public function __call($method, $arguments)
	{
		if (!str_starts_with($method, 'format_as_'))
		{
			throw new \BadMethodCallException("Unsupported method: $method.");
		}

		$as = strtoupper(substr($method, strlen('format_as_')));
		$format = constant(__CLASS__ . '::' . $as);
		assert(is_string($format));
		$value = $this->format($format);

		return match ($as)
		{
			'RFC822', 'RFC1123' => str_replace('+0000', 'GMT', $value),
			'ISO8601' => str_replace('+0000', 'Z', $value),
			default => $value,
		};
	}

	/**
	 * Returns the datetime formatted as {@see ISO8601}.
	 *
	 * @return string The instance rendered as an {@see ISO8601} string, or an empty string if the
	 * datetime is empty.
	 */
	public function __toString(): string
	{
		return $this->is_empty ? "" : $this->as_iso8601;
	}

	/**
	 * Returns a {@see ISO8601} representation of the instance.
	 */
	public function jsonSerialize(): string
	{
		return (string) $this;
	}

	/**
	 * @inheritdoc
	 *
	 * The timezone can be specified as a string.
	 *
	 * If the timezone is `local` the timezone returned by {@see date_default_timezone_get()} is
	 * used instead.
	 *
	 * @param DateTimeZone|string $timezone
	 *
	 * @throws \DateInvalidTimeZoneException
	 */
	public function setTimezone($timezone): self
	{
		if ($timezone === 'local')
		{
			$timezone = date_default_timezone_get();
		}

		if (!$timezone instanceof DateTimeZone)
		{
			$timezone = new DateTimeZone($timezone);
		}

		return parent::setTimezone($timezone);
	}

	/**
	 * Modifies the properties of the instance according to the options.
	 *
	 * The following properties can be updated: {@see $year}, {@see $month}, {@see $day},
	 * {@see $hour}, {@see $minute} and {@see $second}.
	 *
	 * Note: Values exceeding ranges are added to their parent values.
	 *
	 * <pre>
	 * <?php
	 *
	 * use ICanBoogie\DateTime;
	 *
	 * $time = new DateTime('now');
	 * $time->change([ 'year' => 2000, 'second' => 0 ]);
	 * </pre>
	 *
	 * @param array{ year: int, month: int, day: int, hour: int, minute: int, second: int,
	 *     timezone: string } $options
	 * @param bool $cascade If `true`, time options (`hour`, `minute`, `second`) reset
	 * cascading, so if only the hour is passed, then minute and second are set to 0. If the hour
	 * and minute are passed, the second is set to 0.
	 *
	 * @throws \DateInvalidTimeZoneException
	 */
	public function change(array $options, bool $cascade = false): self
	{
		static $default_options = [

			'year' => null,
			'month' => null,
			'day' => null,
			'hour' => null,
			'minute' => null,
			'second' => null,
			'timezone' => null,

		];

		$options = array_intersect_key($options + $default_options, $default_options);

		$year = $options['year'] ?? null;
		$month = $options['month'] ?? null;
		$day = $options['day'] ?? null;
		$hour = $options['hour'] ?? null;
		$minute = $options['minute'] ?? null;
		$second = $options['second'] ?? null;
		$timezone = $options['timezone'] ?? null;

		if ($timezone !== null)
		{
			$this->setTimezone($timezone);
		}

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
	 * Instantiate a new instance with changes properties.
	 *
	 * @param array{ year: int, month: int, day: int, hour: int, minute: int, second: int,
	 *     timezone: string } $options
	 * @param bool $cascade If `true`, time options (`hour`, `minute`, `second`) reset
	 * cascading, so if only the hour is passed, then minute and second are set to 0. If the hour
	 * and minute are passed, the second is set to 0.
	 *
	 * @throws \DateInvalidTimeZoneException
	 */
	public function with(array $options, bool $cascade = false): self
	{
		$dt = clone $this;

		return $dt->change($options, $cascade);
	}

	/**
	 * If the instance represents an empty date and the format is {@see DATE} or {@see DB},
	 * an empty date is returned, respectively "0000-00-00" and "0000-00-00 00:00:00". Note that
	 * the time information is discarded for {@see DB}. This only applies to {@see DATE} and
	 * {@see DB} formats. For instance {@see RSS} will return the following string:
	 * "Wed, 30 Nov -0001 00:00:00 +0000".
	 *
	 * @inheritdoc
	 */
	public function format($format): string
	{
		if (($format == self::DATE || $format == self::DB) && $this->is_empty)
		{
			return $format == self::DATE ? '0000-00-00' : '0000-00-00 00:00:00';
		}

		return parent::format($format);
	}

	/**
	 * Returns a localized instance.
	 *
	 * @return mixed
	 *
	 * @throws \RuntimeException if {@see $localizer} is not defined.
	 */
	public function localize(string $locale = 'en')
	{
		$localizer = self::$localizer
			?? throw new \RuntimeException("Localizer is not defined yet.");

		return $localizer($this, $locale);
	}
}
