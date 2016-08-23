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
 * Representation of a time zone location.
 *
 * <pre>
 * <?php
 *
 * use ICanBoogie\TimeZoneLocation;
 *
 * $zone = new \DateTimeZone('Europe/Paris');
 * $location = new TimeZoneLocation($zone->getLocation());
 *
 * echo $location;               // FR,48.86667,2.33333
 * echo $location->country_code; // FR
 * echo $location->latitude;     // 48.86667
 * echo $location->longitude;    // 2.33333
 *
 * $location->latitude = true;   // throws ICanBoogie\PropertyNotWritable
 * </pre>
 *
 * @property-read string $country_code The country code of the location.
 * @property-read float $latitude The latitude of the location.
 * @property-read float $longitude The longitude of the location.
 * @property-read string $comments Comments on the location.
 */
class TimeZoneLocation
{
	static private $cache;

	/**
	 * Creates an instance from a {@link \DateTimeZone} instance.
	 *
	 * @param \DateTimeZone $zone
	 *
	 * @return \ICanBoogie\TimeZoneLocation
	 */
	static public function from(\DateTimeZone $zone)
	{
		$hash = spl_object_hash($zone);

		if (empty(self::$cache[$hash]))
		{
			self::$cache[$hash] = new static($zone->getLocation());
		}

		return self::$cache[$hash];
	}

	private $location;

	/**
	 * Initializes the {@link $location} property.
	 *
	 * @param array $location Location information provided by {@link \DateTimeZone::getLocation()}.
	 */
	public function __construct(array $location)
	{
		$this->location = $location;
	}

	/**
	 * Returns the {@link $country_code}, {@link $latitude}, {@link $longitude} and
	 * {@link $comments} properties.
	 *
	 * @throws \LogicException in attempt to read an undefined property.
	 *
	 * @inheritdoc
	 */
	public function __get($property)
	{
		if (isset($this->location[$property]))
		{
			return $this->location[$property];
		}

		throw new \LogicException("Property is not defined: $property.");
	}

	/**
	 * @throws \LogicException in attempt to write an undefined property.
	 *
	 * @inheritdoc
	 */
	public function __set($property, $value)
	{
		throw new \LogicException("Property is not defined: $property.");
	}

	/**
	 * Returns the instance formatted as "{$country_code},{$latitude},{$longitude}".
	 *
	 * @return string
	 */
	public function __toString()
	{
		return "{$this->country_code},{$this->latitude},{$this->longitude}";
	}
}
