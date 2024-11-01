<?php

namespace Test\ICanBoogie;

use DateTimeZone;
use ICanBoogie\TimeZoneLocation;
use PHPUnit\Framework\TestCase;

final class TimeZoneLocationTest extends TestCase
{
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

	public function test_properties(): void
	{
		$zone = new DateTimeZone('Europe/Paris');
		$location = $zone->getLocation();
		$instance = new TimeZoneLocation($location);

		$this->assertSame($location, $instance->location);
		$this->assertEquals($location['country_code'], $instance->country_code);
		$this->assertEquals($location['latitude'], $instance->latitude);
		$this->assertEquals($location['longitude'], $instance->longitude);
		$this->assertEquals($location['comments'], $instance->comments);
	}

	public function test_to_string(): void
	{
		$zone = new DateTimeZone('Europe/Paris');
		$location = $zone->getLocation();
		$instance = new TimeZoneLocation($location);

		$this->assertEquals("$location[country_code],$location[latitude],$location[longitude]", (string) $instance);
	}
}
