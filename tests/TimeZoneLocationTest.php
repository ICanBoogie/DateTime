<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\ICanBoogie;

use DateTimeZone;
use ICanBoogie\DateTime;
use ICanBoogie\PropertyNotDefined;
use ICanBoogie\PropertyNotWritable;
use ICanBoogie\TimeZoneLocation;
use PHPUnit\Framework\TestCase;
use function array_map;
use function preg_split;
use function trim;

class TimeZoneLocationTest extends TestCase
{

	/**
	 * @dataProvider provide_readonly
	 */
	public function test_readonly(string $property): void
	{
		$zone = new DateTimeZone('Europe/Paris');
		$location = $zone->getLocation();
		$instance = new TimeZoneLocation($location);

		$this->expectExceptionMessage("The property `$property` for object of class `ICanBoogie\TimeZoneLocation` is not writable.");
		$this->expectException(PropertyNotWritable::class);
		$instance->$property = null;
	}

	public function provide_readonly(): array
	{
		$properties = <<<EOT
		country_code
		latitude
		longitude
		comments
		unsupported_property
		EOT;

		return array_map(
			function ($v) { return [ $v ]; },
			preg_split("/\s+/", trim($properties))
		);
	}

	public function test_from(): void
	{
		$zone = new DateTimeZone('Europe/Paris');
		$location = TimeZoneLocation::from($zone);

		$this->assertInstanceOf(TimeZoneLocation::class, $location);
	}

	public function test_from_cache(): void
	{
		$zone = new DateTimeZone('Europe/Paris');
		$location = TimeZoneLocation::from($zone);
		$cached = TimeZoneLocation::from($zone);

		$this->assertEquals(spl_object_hash($location), spl_object_hash($cached));
	}

	public function test_location(): void
	{
		$zone = new DateTimeZone('Europe/Paris');
		$location = $zone->getLocation();
		$l = new TimeZoneLocation($location);

		$this->assertEquals("FR", $l->country_code);
	}

	public function test_get_country_code(): void
	{
		$zone = new DateTimeZone('Europe/Paris');
		$location = $zone->getLocation();
		$instance = new TimeZoneLocation($location);

		$this->assertEquals($location['country_code'], $instance->country_code);
	}

	public function test_get_latitude(): void
	{
		$zone = new DateTimeZone('Europe/Paris');
		$location = $zone->getLocation();
		$instance = new TimeZoneLocation($location);

		$this->assertEquals($location['latitude'], $instance->latitude);
	}

	public function test_get_longitude(): void
	{
		$zone = new DateTimeZone('Europe/Paris');
		$location = $zone->getLocation();
		$instance = new TimeZoneLocation($location);

		$this->assertEquals($location['longitude'], $instance->longitude);
	}

	public function test_get_comments(): void
	{
		$zone = new DateTimeZone('Europe/Paris');
		$location = $zone->getLocation();
		$instance = new TimeZoneLocation($location);

		$this->assertEquals($location['comments'], $instance->comments);
	}

	public function test_get_unsupported_property(): void
	{
		$zone = new DateTimeZone('Europe/Paris');
		$location = $zone->getLocation();
		$instance = new TimeZoneLocation($location);

		$this->expectExceptionMessage("Undefined property `you_know_that_i_m_no_good` for object of class `ICanBoogie\TimeZoneLocation`.");
		$this->expectException(PropertyNotDefined::class);
		$instance->you_know_that_i_m_no_good;
	}

	public function test_to_string(): void
	{
		$zone = new DateTimeZone('Europe/Paris');
		$location = $zone->getLocation();
		$instance = new TimeZoneLocation($location);

		$this->assertEquals("$location[country_code],$location[latitude],$location[longitude]", (string) $instance);
	}
}
