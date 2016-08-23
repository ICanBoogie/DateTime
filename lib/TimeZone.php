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
 * Representation of a timezone.
 *
 * <pre>
 * <?php
 *
 * use ICanBoogie\TimeZone;
 *
 * $zone = new TimeZone('Europe/Paris');
 *
 * echo $zone;                     // "Europe/Paris"
 * echo $zone->offset;             // 3600
 * echo $zone->location;           // FR,48.86667,2.33333
 * echo $zone->location->latitude; // 48.86667
 * </pre>
 *
 * @property-read TimeZoneLocation $location Location information for the timezone.
 * @property-read string $name Name of the timezone.
 * @property-read int $offset Timezone offset from UTC.
 * @property-read bool $is_utc `true` Time zone is `UTC`, `false` otherwise.
 * @property-read bool $is_local `true` if time zone is local, `false` otherwise.
 */
class TimeZone extends \DateTimeZone
{
	static private $utc_time;
	static private $cache;

	/**
	 * Returns a timezone according to the specified source.
	 *
	 * If the source is already an instance of {@link Zone}, it is returned as is.
	 *
	 * Note: Instances created by the method are shared. That is, equivalent sources yield
	 * the same instance.
	 *
	 * @param mixed $source Source of the timezone.
	 *
	 * @return TimeZone
	 */
	static public function from($source)
	{
		if ($source instanceof self)
		{
			return $source;
		}
		else if ($source instanceof \DateTimeZone)
		{
			$source = $source->getName();
		}

		$source = (string) $source;

		if (empty(self::$cache[$source]))
		{
			self::$cache[$source] = new static($source);
		}

		return self::$cache[$source];
	}

	/**
	 * The name of the timezone.
	 *
	 * Note: This variable is only used to provide information during debugging.
	 *
	 * @var string
	 */
	private $name;

	/**
	 * Location of the timezone.
	 *
	 * @var TimeZoneLocation
	 */
	private $location;

	/**
	 * Initializes the {@link $name} property.
	 *
	 * @param string $timezone
	 */
	public function __construct($timezone)
	{
		parent::__construct($timezone);

		$name = $this->getName();

		if ($name == 'utc')
		{
			$name = 'UTC'; // @codeCoverageIgnore
		}

		$this->name = $name;
	}

	/**
	 * @throws \LogicException in attempt to get an undefined property.
	 *
	 * @inheritdoc
	 */
	public function __get($property)
	{
		$getter = "get_$property";

		if (!method_exists($this, $getter))
		{
			throw new \LogicException("Property no defined: $property.");
		}

		return $this->$getter();
	}

	/**
	 * @inheritdoc
	 *
	 * @throws \LogicException in attempt to write any property.
	 */
	public function __set($property, $value)
	{
		throw new \LogicException("Property no writable: $property.");
	}

	/**
	 * @return TimeZoneLocation
	 */
	protected function get_location()
	{
		$location = &$this->location;

		return $location ?: $location = TimeZoneLocation::from($this);
	}

	/**
	 * @return string
	 */
	protected function get_name()
	{
		return $this->name;
	}

	/**
	 * @return int
	 */
	protected function get_offset()
	{
		$utc_time = &self::$utc_time;

		if (!$utc_time)
		{
			$utc_time = new \DateTime('now', new \DateTimeZone('utc'));
		}

		return $this->getOffset($utc_time);
	}

	/**
	 * @return bool `true` if time zone is `UTC`, `false` otherwise.
	 */
	protected function get_is_utc()
	{
		return $this->name === 'UTC';
	}

	/**
	 * @return bool `true` if time zone is local, `false` otherwise.
	 */
	protected function get_is_local()
	{
		return $this->name === date_default_timezone_get();
	}

	/**
	 * Returns the name of the timezone.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->name;
	}
}
