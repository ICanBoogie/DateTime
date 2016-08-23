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

use ICanBoogie\DateTime;
use ICanBoogie\MutableDateTime;
use ICanBoogie\TimeZone;

/**
 * @property-read int $timestamp Unix timestamp.
 * @property-read int $year Year.
 * @property-read int $month Month of the year.
 * @property-read int $day Day of the month.
 * @property-read int $hour Hour of the day.
 * @property-read int $minute Minute of the hour.
 * @property-read int $second Second of the minute.
 * @property-read int $quarter Quarter of the year.
 * @property-read int $week Week of the year.
 * @property-read int $weekday Day of the week.
 *
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
 * @property-read TimeZone $zone The timezone of the instance.
 * @property-read bool $is_utc `true` if the instance is in the UTC timezone.
 * @property-read bool $is_local `true` if the instance is in the local timezone.
 * @property-read bool $is_dst `true` if time occurs during Daylight Saving Time in its time zone.
 *
 * @property-read DateTime $immutable An immutable representation of the instance.
 * @property-read MutableDateTime $mutable A mutable representation of the instance.
 */
trait Readers
{
	/**
	 * @param string $property
	 *
	 * @return mixed
	 *
	 * @throws \LogicException in attempt to obtain an undefined property.
	 */
	public function __get($property)
	{
		if (strpos($property, 'as_') === 0)
		{
			return $this->{ 'format_' . $property }();
		}

		switch ($property)
		{
			case 'timestamp':
				return $this->getTimestamp();

			case 'year':
				return (int) $this->format('Y');
			case 'month':
				return (int) $this->format('m');
			case 'day':
				return (int) $this->format('d');
			case 'hour':
				return (int) $this->format('H');
			case 'minute':
				return (int) $this->format('i');
			case 'second':
				return (int) $this->format('s');
			case 'quarter':
				return floor(($this->month - 1) / 3) + 1;
			case 'week':
				return (int) $this->format('W');
			case 'year_day':
				return (int) $this->format('z') + 1;
			case 'weekday':
				return (int) $this->format('w') ?: 7;

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
			case 'tomorrow':
			case 'yesterday':
			case 'mutable':
			case 'immutable':
				return $this->{ 'get_' . $property }();

			case 'zone':
				return TimeZone::from($this->getTimezone());
			case 'utc':
			case 'local':
				$datetime = clone $this; // works for immutable and mutable
				return $datetime->setTimezone($property);
			case 'is_utc':
				return $this->zone->name == 'UTC';
			case 'is_local':
				return $this->zone->name == date_default_timezone_get();
			case 'is_dst':
				$timestamp = $this->timestamp;
				$transitions = $this->zone->getTransitions($timestamp, $timestamp);
				return $transitions[0]['isdst'];
		}

		throw new \LogicException("Property is not defined: $property.");
	}

	/**
	 * Returns Monday of the week.
	 *
	 * @return DateTime
	 */
	protected function get_monday()
	{
		$datetime = clone $this;
		$day = $datetime->weekday;

		if ($day != 1)
		{
			$datetime = $datetime->modify('-' . ($day - 1) . ' day');
		}

		return $datetime->setTime(0, 0, 0);
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
		$datetime = clone $this;
		$day = $this->weekday;

		if ($day != 7)
		{
			$datetime = $datetime->modify('+' . (7 - $day) . ' day');
		}

		return $datetime->setTime(0, 0, 0);
	}

	/**
	 * @return DateTime
	 */
	protected function get_tomorrow()
	{
		return $this
			->modify('+1 day')
			->setTime(0, 0, 0);
	}

	/**
	 * @return DateTime
	 */
	protected function get_yesterday()
	{
		return $this
			->modify('-1 day')
			->setTime(0, 0, 0);
	}

	/**
	 * @return MutableDateTime
	 */
	protected function get_mutable()
	{
		return MutableDateTime::from($this);
	}

	/**
	 * @return DateTime
	 */
	protected function get_immutable()
	{
		return DateTime::from($this);
	}
}
