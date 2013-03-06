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
 * </pre>
 *
 * @property-read string $country_code The country code of the location.
 * @property-read float $latitude The latitude of the location.
 * @property-read float $longitude The longitude of the location.
 * @property-read string $comments Comments on the location.
 */
class TimeZoneLocation
{
	/**
	 * The country code of the location.
	 *
	 * @var string
	 */
	protected $country_code;

	/**
	 * The latitude of the location.
	 *
	 * @var float
	 */
	protected $latitude;

	/**
	 * The longitude of the location.
	 *
	 * @var float
	 */
	protected $longitude;

	/**
	 * Comments on the location.
	 *
	 * @var string
	 */
	protected $comments;

	/**
	 * Initializes the {@link $country_code}, {@link $latitude}, {@link $longitude} and
	 * {@link $comments} properties.
	 *
	 * @param array $location Location information provided by {@link \DateTimeZone::getLocation()}.
	 *
	 * @throws \InvalidArgumentException In attempt to initialize an unsupported property.
	 */
	public function __construct(array $location)
	{
		foreach ($location as $property => $value)
		{
			if (!property_exists($this, $property))
			{
				throw new \InvalidArgumentException("Unsupported property: $property.");
			}

			$this->$property = $value;
		}
	}

	/**
	 * Returns the {@link $country_code}, {@link $latitude}, {@link $longitude} and
	 * {@link $comments} properties.
	 *
	 * @param string $property
	 *
	 * @throws PropertyNotDefined in attempt to get an unsupported property.
	 */
	public function __get($property)
	{
		if (property_exists($this, $property))
		{
			return $this->$property;
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
	 * Returns the instance formatted as "{$country_code},{$latitude},{$longitude}".
	 *
	 * @return string
	 */
	public function __toString()
	{
		return "{$this->country_code},{$this->latitude},{$this->longitude}";
	}
}