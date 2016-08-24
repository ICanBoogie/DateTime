<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie\DateTime;

use ICanBoogie\ImmutableDateTime;

/**
 * @property-read int $year Year.
 * @property-read int $month Month of the year.
 * @property-read int $day Day of the month.
 * @property-read int $hour Hour of the day.
 * @property-read int $minute Minute of the hour.
 * @property-read int $second Second of the minute.
 *
 * @property-read bool $is_empty `true` if the instance represents an empty date such as "0000-00-00" or "0000-00-00 00:00:00".
 *
 * @property-read string $as_iso8601 The instance formatted according to {@link ISO8601}.
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
 */
trait Shared
{
	/**
	 * @return int
	 */
	abstract public function getTimestamp();

	/**
	 * @return \DateTimeZone
	 */
	abstract public function getTimezone();

	/**
	 * @param int $year
	 * @param int $month
	 * @param int $day
	 *
	 * @return \DateTime|\DateTimeImmutable
	 */
	abstract public function setDate($year, $month, $day);

	/**
	 * @param int $hour
	 * @param int $minute
	 * @param int|null $second
	 *
	 * @return \DateTime|\DateTimeImmutable
	 */
	abstract public function setTime($hour, $minute, $second = null);

	/**
	 * @inheritdoc
	 */
	static public function from($source, $timezone = null)
	{
		if ($source instanceof static)
		{
			return clone $source;
		}

		if ($source instanceof \DateTimeInterface)
		{
			return new static($source->format('Y-m-d H:i:s.u'), $source->getTimezone());
		}

		return new static($source, $timezone);
	}

	/**
	 * @inheritdoc
	 */
	static public function none($timezone = 'utc')
	{
		return new static('0000-00-00', $timezone);
	}

	/**
	 * @inheritdoc
	 */
	static public function right_now()
	{
		return new static;
	}

	/**
	 * If the time zone is specified as a string a {@link \DateTimeZone} instance is created and
	 * used instead.
	 *
	 * <pre>
	 * <?php
	 *
	 * use ICanBoogie\ImmutableDateTime as DateTime;
	 *
	 * new DateTime('2001-01-01 01:01:01', new \DateTimeZone('Europe/Paris')));
	 * new DateTime('2001-01-01 01:01:01', 'Europe/Paris');
	 * new DateTime;
	 * </pre>
	 *
	 * @param string $time Defaults to "now".
	 * @param \DateTimeZone|string|null $timezone
	 */
	public function __construct($time = 'now', $timezone = null)
	{
		if (is_string($timezone))
		{
			$timezone = new \DateTimeZone($timezone);
		}

		parent::__construct($time, $timezone);
	}

	/**
	 * Handles the `format_as_*` methods.
	 *
	 * @throws \BadMethodCallException in attempt to call an unsupported method.
	 *
	 * @inheritdoc
	 *
	 * @see format_as()
	 */
	public function __call($method, $arguments)
	{
		if (strpos($method, 'format_as_') === 0)
		{
			return $this->format_as(substr($method, strlen('format_as_')));
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

		if (!$timezone instanceof \DateTimeZone)
		{
			$timezone = new \DateTimeZone($timezone);
		}

		return parent::setTimezone($timezone);
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

	/**
	 * @inheritdoc
	 */
	public function change(array $options, $cascade = false)
	{
		static $default_options = [

			'year' => null,
			'month' => null,
			'day' => null,
			'hour' => null,
			'minute' => null,
			'second' => null

		];

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

		$datetime = $this;

		if ($year !== null || $month !== null || $day !== null)
		{
			$datetime = $datetime->setDate
			(
				$year === null ? $this->year : $year,
				$month === null ? $this->month : $month,
				$day === null ? $this->day : $day
			);
		}

		if ($hour !== null || $minute !== null || $second !== null)
		{
			$datetime = $datetime->setTime
			(
				$hour === null ? $this->hour : $hour,
				$minute === null ? $this->minute : $minute,
				$second === null ? $this->second : $second
			);
		}

		return $datetime;
	}

	/**
	 * @inheritdoc
	 */
	public function localize($locale = 'en')
	{
		$localizer = ImmutableDateTime::$localizer;

		if (!$localizer)
		{
			throw new \LogicException("Localizer is not defined yet.");
		}

		return $localizer($this, $locale);
	}

	/**
	 * If the format is {@link RFC822} or {@link RFC1123} and the time zone is equivalent to GMT,
	 * the offset `+0000` is replaced by `GMT` according to the specs.
	 *
	 * If the format is {@link ISO8601} and the time zone is equivalent to UTC, the offset `+0000`
	 * is replaced by `Z` according to the specs.
	 *
	 * @param string $as
	 *
	 * @return string
	 */
	private function format_as($as)
	{
		$as = strtoupper($as);
		$format = constant(ImmutableDateTime::class . '::' . $as);
		$value = $this->format($format);

		switch ($as)
		{
			case 'RFC822':
			case 'RFC1123':
				return str_replace('+0000', 'GMT', $value);

			case 'ISO8601':
				return str_replace('+0000', 'Z', $value);

			default:
				return $value;
		}
	}
}
