<?php

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
 */
final class TimeZoneLocation
{
	/**
	 * @var array<string, self>
	 */
	private static array $cache;

	/**
	 * Creates an instance from a {@see \DateTimeZone} instance.
	 */
	static public function from(\DateTimeZone $zone): self
	{
		$hash = spl_object_hash($zone);
		$location = $zone->getLocation()
			?: throw new \RunTimeException("Unable to get location for zone $hash");

		return self::$cache[$hash] ??= new self($location);
	}

	/**
	 * The country code of the location.
	 */
	public readonly string $country_code;

	/**
	 * The latitude of the location.
	 */
	public readonly float $latitude;

	/**
	 * The longitude of the location.
	 */
	public readonly float $longitude;

	/**
	 * Comments on the location.
	 */
	public readonly string $comments;

	/**
	 * @param array{
	 *     country_code: string,
	 *     latitude: float,
	 *     longitude: float,
	 *     comments: string } $location Location information provided by {@see \DateTimeZone::getLocation()}.
	 */
	public function __construct(
		public readonly array $location
	) {
		$this->country_code = $location['country_code'];
		$this->latitude = $location['latitude'];
		$this->longitude = $location['longitude'];
		$this->comments = $location['comments'];
	}

	/**
	 * Returns the instance formatted as "{$country_code},{$latitude},{$longitude}".
	 */
	public function __toString(): string
	{
		return "$this->country_code,$this->latitude,$this->longitude";
	}
}
