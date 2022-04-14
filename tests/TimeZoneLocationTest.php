<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\ICanBoogie;

use ICanBoogie\TimeZoneLocation;
use PHPUnit\Framework\TestCase;

class TimeZoneLocationTest extends TestCase
{
	public function test_from()
	{
		$zone = new \DateTimeZone('Europe/Paris');
		$location = TimeZoneLocation::from($zone);

		$this->assertInstanceOf(TimeZoneLocation::class, $location);
	}

	public function test_from_cache()
	{
		$zone = new \DateTimeZone('Europe/Paris');
		$location = TimeZoneLocation::from($zone);
		$cached = TimeZoneLocation::from($zone);

		$this->assertEquals(spl_object_hash($location), spl_object_hash($cached));
	}

	public function test_location(): void
	{
		$zone = new \DateTimeZone('Europe/Paris');
		$location = $zone->getLocation();
		$tzl = new TimeZoneLocation($location);

		$this->assertEquals('FR', $tzl->country_code);
	}

	public function test_get_coutry_code()
	{
		$zone = new \DateTimeZone('Europe/Paris');
		$location = $zone->getLocation();
		$instance = new TimeZoneLocation($location);

		$this->assertEquals($location['country_code'], $instance->country_code);
	}

	public function test_set_country_code()
	{
		$zone = new \DateTimeZone('Europe/Paris');
		$location = $zone->getLocation();
		$instance = new TimeZoneLocation($location);

		$this->expectExceptionMessage("The property `country_code` for object of class `ICanBoogie\TimeZoneLocation` is not writable.");
		$this->expectException(\ICanBoogie\PropertyNotWritable::class);
		$instance->country_code = true;
	}

	public function test_get_latitude()
	{
		$zone = new \DateTimeZone('Europe/Paris');
		$location = $zone->getLocation();
		$instance = new TimeZoneLocation($location);

		$this->assertEquals($location['latitude'], $instance->latitude);
	}

	public function test_set_latitude()
	{
		$zone = new \DateTimeZone('Europe/Paris');
		$location = $zone->getLocation();
		$instance = new TimeZoneLocation($location);

		$this->expectExceptionMessage("The property `latitude` for object of class `ICanBoogie\TimeZoneLocation` is not writable.");
		$this->expectException(\ICanBoogie\PropertyNotWritable::class);
		$instance->latitude = true;
	}

	public function test_get_longitude()
	{
		$zone = new \DateTimeZone('Europe/Paris');
		$location = $zone->getLocation();
		$instance = new TimeZoneLocation($location);

		$this->assertEquals($location['longitude'], $instance->longitude);
	}

	public function test_set_longitude()
	{
		$zone = new \DateTimeZone('Europe/Paris');
		$location = $zone->getLocation();
		$instance = new TimeZoneLocation($location);

		$this->expectExceptionMessage("The property `longitude` for object of class `ICanBoogie\TimeZoneLocation` is not writable.");
		$this->expectException(\ICanBoogie\PropertyNotWritable::class);
		$instance->longitude = true;
	}

	public function test_get_comments()
	{
		$zone = new \DateTimeZone('Europe/Paris');
		$location = $zone->getLocation();
		$instance = new TimeZoneLocation($location);

		$this->assertEquals($location['comments'], $instance->comments);
	}

	public function test_set_comments()
	{
		$zone = new \DateTimeZone('Europe/Paris');
		$location = $zone->getLocation();
		$instance = new TimeZoneLocation($location);

		$this->expectExceptionMessage("The property `comments` for object of class `ICanBoogie\TimeZoneLocation` is not writable.");
		$this->expectException(\ICanBoogie\PropertyNotWritable::class);
		$instance->comments = true;
	}

	public function test_get_unsupported_property()
	{
		$zone = new \DateTimeZone('Europe/Paris');
		$location = $zone->getLocation();
		$instance = new TimeZoneLocation($location);

		$this->expectExceptionMessage("Undefined property `you_know_that_i_m_no_good` for object of class `ICanBoogie\TimeZoneLocation`.");
		$this->expectException(\ICanBoogie\PropertyNotDefined::class);
		$instance->you_know_that_i_m_no_good;
	}

	public function test_set_unsupported_property()
	{
		$zone = new \DateTimeZone('Europe/Paris');
		$location = $zone->getLocation();
		$instance = new TimeZoneLocation($location);

		$this->expectExceptionMessage("The property `unsupported_property` for object of class `ICanBoogie\TimeZoneLocation` is not writable.");
		$this->expectException(\ICanBoogie\PropertyNotWritable::class);
		$instance->unsupported_property = true;
	}

	public function test_to_string()
	{
		$zone = new \DateTimeZone('Europe/Paris');
		$location = $zone->getLocation();
		$instance = new TimeZoneLocation($location);

		$this->assertEquals("$location[country_code],$location[latitude],$location[longitude]", (string) $instance);
	}
}
