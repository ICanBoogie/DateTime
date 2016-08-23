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
 * @group datetime
 * @group timezone
 */
class TimeZoneTest extends \PHPUnit_Framework_TestCase
{
	public function test_get_location()
	{
		$z = new TimeZone('Europe/Paris');
		$this->assertInstanceOf('ICanBoogie\TimeZoneLocation', $z->location);
	}

	public function test_get_name()
	{
		$z = new TimeZone('Europe/Paris');
		$this->assertEquals($z->getName(), $z->name);
	}

	public function test_get_offset()
	{
		$z = new TimeZone('Europe/Paris');
		$utc = new \DateTime('now', new \DateTimeZone('utc'));

		$this->assertEquals($z->getOffset($utc), $z->offset);
	}

	public function test_utc_case()
	{
		$this->assertEquals('UTC', TimeZone::from('utc')->name);
		$this->assertEquals('UTC', TimeZone::from('UTC')->name);
		$this->assertEquals('UTC', (new TimeZone('utc'))->name);
		$this->assertEquals('UTC', (new TimeZone('UTC'))->name);
	}

	public function test_should_create_from_php_datetimezone()
	{
		$name = 'Europe/Paris';
		$z1 = new \DateTimeZone($name);
		$z2 = TimeZone::from($z1);

		$this->assertSame($name, $z2->name);
	}

	public function test_should_reuse_instance()
	{
		$z1 = TimeZone::from('utc');
		$z2 = TimeZone::from('utc');

		$this->assertSame($z1, $z2);
	}

	public function test_should_reuse_timezone()
	{
		$z1 = TimeZone::from('Europe/Paris');
		$z2 = TimeZone::from($z1);

		$this->assertSame($z1, $z2);
	}

	public function test_to_string()
	{
		$name = 'Europe/Paris';
		$z1 = TimeZone::from($name);

		$this->assertSame($name, (string) $z1);
	}

	/**
	 * @dataProvider provide_test_is_utc
	 *
	 * @param string $name
	 * @param bool $is_utc
	 */
	public function test_is_utc($name, $is_utc)
	{
		$this->assertSame($is_utc, TimeZone::from($name)->is_utc);
	}

	/**
	 * @return array
	 */
	public function provide_test_is_utc()
	{
		return [

			[ 'Europe/Paris', false ],
			[ 'GMT', false ],
			[ 'gmt', false ],
			[ 'UTC', true ],
			[ 'utc', true ],

		];
	}

	/**
	 * @dataProvider provide_test_is_local
	 *
	 * @param string $name
	 * @param bool $is_local
	 */
	public function test_is_local($name, $is_local)
	{
		$this->assertSame($is_local, TimeZone::from($name)->is_local);
	}

	/**
	 * @return array
	 */
	public function provide_test_is_local()
	{
		return [

			[ 'Asia/Tokyo', false ],
			[ 'GMT', false ],
			[ 'UTC', false ],
			[ date_default_timezone_get(), true ],

		];
	}

	/**
	 * @dataProvider provide_read_only_or_undefined_property
	 * @expectedException \LogicException
	 *
	 * @param string $property
	 */
	public function test_setting_undefined_or_read_only_property_should_throw_exception($property)
	{
		$tz = TimeZone::from('utc');
		$tz->$property = uniqid();
	}

	/**
	 * @return array
	 */
	public function provide_read_only_or_undefined_property()
	{
		$undefined = uniqid();

		return array_map(function ($property) {

			return [ $property ];

		}, explode(' ', "$undefined location name offset is_utc is_local"));
	}
}
