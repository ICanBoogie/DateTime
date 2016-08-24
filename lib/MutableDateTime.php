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
 * @property int $year Year.
 * @property int $month Month of the year.
 * @property int $day Day of the month.
 * @property int $hour Hour of the day.
 * @property int $minute Minute of the hour.
 * @property int $second Second of the minute.
 * @property int $timestamp Unix timestamp.
 *
 * @property TimeZone $timezone The timezone of the instance.
 * @property TimeZone $tz The timezone of the instance.
 * @property-read MutableDateTime $utc A new instance in the UTC timezone.
 * @property-read MutableDateTime $local A new instance in the local timezone.
 *
 * @method $this change(array $options, $cascade = false)
 */
class MutableDateTime extends \DateTime implements \JsonSerializable, AdditionalFormats
{
	use DateTime\Shared;
	use DateTime\Readers;

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
		return empty($_SERVER['REQUEST_TIME']) ? new static : (new static('@' . $_SERVER['REQUEST_TIME']))->local;
	}

	/**
	 * @return MutableDateTime
	 */
	protected function get_local()
	{
		$time = clone $this;
		$time->setTimezone(date_default_timezone_get());

		return $time;
	}

	/**
	 * @return MutableDateTime
	 */
	protected function get_tomorrow()
	{
		$time = clone $this;
		$time->modify('+1 day');
		$time->setTime(0, 0, 0);

		return $time;
	}

	/**
	 * @return MutableDateTime
	 */
	protected function get_yesterday()
	{
		$time = clone $this;
		$time->modify('-1 day');
		$time->setTime(0, 0, 0);

		return $time;
	}

	/**
	 * Sets the {@link $year}, {@link $month}, {@link $day}, {@link $hour}, {@link $minute},
	 * {@link $second}, {@link $timestamp} and {@link $zone} properties.
	 *
	 * @throws \LogicException in attempt to set a read-only or undefined property.
	 *
	 * @inheritdoc
	 */
	public function __set($property, $value)
	{
		static $readonly = [ 'quarter', 'week', 'year_day', 'weekday', 'tomorrow', 'yesterday', 'utc', 'local' ];

		switch ($property)
		{
			case 'year':
			case 'month':
			case 'day':
			case 'hour':
			case 'minute':
			case 'second':
				$this->change([ $property => $value ]);
				return;

			case 'timestamp':
				$this->setTimestamp($value);
				return;

			case 'timezone':
			case 'tz':
				$this->setTimezone($value);
				return;
		}

		if (strpos($property, 'is_') === 0 || strpos($property, 'as_') === 0 || in_array($property, $readonly) || method_exists($this, 'get_' . $property))
		{
			throw new \LogicException("Property is not writable: $property.");
		}

		throw new \LogicException("Property is not defined: $property.");
	}


	/**
	 * Instantiate a new instance with changes properties.
	 *
	 * @param array $options
	 * @param bool $cascade
	 *
	 * @return MutableDateTime
	 */
	public function with(array $options, $cascade = false)
	{
		$dt = clone $this;

		return $dt->change($options, $cascade);
	}
}
