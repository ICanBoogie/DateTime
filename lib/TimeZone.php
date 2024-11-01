<?php

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
 */
class TimeZone extends \DateTimeZone
{
	static private \DateTime $utc_time;

	/**
	 * @var array<string, TimeZone>
	 */
	static private array $cache = [];

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
	 * @throws \DateInvalidTimeZoneException
	 */
	static public function from(self|\DateTimeZone|\Stringable|string $source): self
	{
		if ($source instanceof self)
		{
			return $source;
		}

		if ($source instanceof \DateTimeZone)
		{
			$source = $source->getName();
		}

		$source = (string) $source;

		return self::$cache[$source] ??= new self($source);
	}

	/**
	 * The name of the timezone.
	 *
	 * Note: This variable is only used to provide information during debugging.
	 */
	private string $name;

	/**
	 * Location of the timezone.
	 */
	private TimeZoneLocation $location;

	/**
	 * Initializes the {@see $name} property.
	 *
	 * @throws \DateInvalidTimeZoneException
	 */
	public function __construct(string $timezone)
	{
		parent::__construct($timezone);

		$name = $this->getName();

		if ($name === 'utc')
		{
			$name = 'UTC';
		}

		$this->name = $name;
	}

	/**
	 * Returns the {@see $location}, {@see $name} and {@see $offset} properties.
	 *
	 * @throws PropertyNotDefined in an attempt to get an unsupported property.
	 * @throws \DateMalformedStringException
	 */
	public function __get(string $property)
	{
		switch ($property)
		{
			case 'location':

				return $this->location ??= TimeZoneLocation::from($this);

			case 'name':

				return $this->name;

			case 'offset':

				$utc_time = self::$utc_time ??= new \DateTime('now', new \DateTimeZone('utc'));

				return $this->getOffset($utc_time);
		}

		throw new \LogicException(
			sprintf("Undefined property: %s::%s", $this::class, $property),
		);
	}

	/**
	 * Returns the name of the timezone.
	 */
	public function __toString(): string
	{
		return $this->name;
	}
}
