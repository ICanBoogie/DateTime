<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\ICanBoogie\Time;

use ICanBoogie\TimeZoneLocation;

class TimeZoneLocationTest extends \PHPUnit_Framework_TestCase
{
	public function test_location()
	{
		$zone = new \DateTimeZone('Europe/Paris');
		$location = $zone->getLocation();
		$instance = new TimeZoneLocation($location);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function test_location_with_extraneous()
	{
		$zone = new \DateTimeZone('Europe/Paris');
		$location = $zone->getLocation();
		$location['__extraneous__'] = "You know that I'm no good";
		$instance = new TimeZoneLocation($location);
	}

	public function test_get_coutry_code()
	{
		$zone = new \DateTimeZone('Europe/Paris');
		$location = $zone->getLocation();
		$instance = new TimeZoneLocation($location);

		$this->assertEquals($location['country_code'], $instance->country_code);
	}

	public function test_get_latitude()
	{
		$zone = new \DateTimeZone('Europe/Paris');
		$location = $zone->getLocation();
		$instance = new TimeZoneLocation($location);

		$this->assertEquals($location['latitude'], $instance->latitude);
	}

	public function test_get_longitude()
	{
		$zone = new \DateTimeZone('Europe/Paris');
		$location = $zone->getLocation();
		$instance = new TimeZoneLocation($location);

		$this->assertEquals($location['longitude'], $instance->longitude);
	}

	public function test_get_comments()
	{
		$zone = new \DateTimeZone('Europe/Paris');
		$location = $zone->getLocation();
		$instance = new TimeZoneLocation($location);

		$this->assertEquals($location['comments'], $instance->comments);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotDefined
	 */
	public function test_get_unsupported_property()
	{
		$zone = new \DateTimeZone('Europe/Paris');
		$location = $zone->getLocation();
		$instance = new TimeZoneLocation($location);

		$instance->you_know_that_i_m_no_good;
	}

	public function test_to_string()
	{
		$zone = new \DateTimeZone('Europe/Paris');
		$location = $zone->getLocation();
		$instance = new TimeZoneLocation($location);

		$this->assertEquals("$location[country_code],$location[latitude],$location[longitude]", (string) $instance);
	}
}