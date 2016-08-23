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

class TimeZoneLocationTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var \DateTimeZone
	 */
	private $zone;

	/**
	 * @var array
	 */
	private $data;

	/**
	 * @var TimeZoneLocation
	 */
	private $location;

	public function setUp()
	{
		$this->zone = $zone = new \DateTimeZone('Europe/Paris');
		$this->data = $data = $zone->getLocation();
		$this->location = new TimeZoneLocation($data);
	}

	public function test_from()
	{
		$location = TimeZoneLocation::from($this->zone);

		$this->assertInstanceOf(TimeZoneLocation::class, $location);
	}

	public function test_from_cache()
	{
		$zone = $this->zone;
		$location = TimeZoneLocation::from($zone);
		$cached = TimeZoneLocation::from($zone);

		$this->assertSame($location, $cached);
	}

	/**
	 * @dataProvider provide_mapped_properties
	 *
	 * @param string $property
	 */
	public function test_data_mapping($property)
	{
		$this->assertEquals($this->data[$property], $this->location->$property);
	}

	/**
	 * @return array
	 */
	public function provide_mapped_properties()
	{
		return array_map(function ($property) {

			return [ $property ];

		}, explode(' ', "country_code latitude longitude comments"));
	}

	public function test_to_string()
	{
		$data = $this->data;

		$this->assertEquals("$data[country_code],$data[latitude],$data[longitude]", (string) $this->location);
	}

	/**
	 * @dataProvider provide_read_only_or_undefined_property
	 * @expectedException \LogicException
	 *
	 * @param string $property
	 */
	public function test_setting_undefined_or_read_only_property_should_throw_exception($property)
	{
		$this->location->$property = uniqid();
	}

	/**
	 * @return array
	 */
	public function provide_read_only_or_undefined_property()
	{
		$undefined = uniqid();

		return array_map(function ($property) {

			return [ $property ];

		}, explode(' ', "$undefined country_code latitude longitude comments"));
	}
}
