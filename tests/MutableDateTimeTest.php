<?php

namespace Tests\ICanBoogie;

use ICanBoogie\MutableDateTime;

class MutableDateTimeTest extends \PHPUnit_Framework_TestCase
{
	public function test_now()
	{
		$now = MutableDateTime::now();

		$this->assertNotSame($now, MutableDateTime::now());
	}

	public function test_set_year()
	{
		$d = new MutableDateTime('2001-01-01 01:01:01');
		$d->year = 2009;
		$this->assertEquals('2009-01-01 01:01:01', $d->as_db);
	}

	public function test_set_month()
	{
		$d = new MutableDateTime('2001-01-01 01:01:01');
		$d->month = 9;
		$this->assertEquals('2001-09-01 01:01:01', $d->as_db);
	}

	public function test_set_day()
	{
		$d = new MutableDateTime('2001-01-01 01:01:01');
		$d->day = 9;
		$this->assertEquals('2001-01-09 01:01:01', $d->as_db);
	}

	public function test_set_hour()
	{
		$d = new MutableDateTime('2001-01-01 01:01:01');
		$d->hour = 9;
		$this->assertEquals('2001-01-01 09:01:01', $d->as_db);
	}

	public function test_set_minute()
	{
		$d = new MutableDateTime('2001-01-01 01:01:01');
		$d->minute = 9;
		$this->assertEquals('2001-01-01 01:09:01', $d->as_db);
	}

	public function test_set_second()
	{
		$d = new MutableDateTime('2001-01-01 01:01:01');
		$d->second = 9;
		$this->assertEquals('2001-01-01 01:01:09', $d->as_db);
	}

	public function test_set_timestamp()
	{
		$d = new MutableDateTime(null, 'utc');
		$d->timestamp = 234446400;
		$this->assertSame("1977-06-06T12:00:00Z", (string) $d);
	}

	public function test_set_zone()
	{
		$d = new MutableDateTime('1977-06-06T12:00:00', 'Asia/Tokyo');
		$this->assertSame('1977-06-06T12:00:00+0900', (string) $d);
		$d->zone = 'Europe/Paris';
		$this->assertSame('1977-06-06T05:00:00+0200', (string) $d);
	}

	/**
	 * @dataProvider provide_not_writable_properties
	 * @expectedException \ICanBoogie\PropertyNotWritable
	 */
	public function test_setting_unwritable_properties_should_throw_exception($property)
	{
		$d = new MutableDateTime;
		$d->$property = uniqid();
	}

	/**
	 * @return array
	 */
	public function provide_not_writable_properties()
	{
		return array_map(function ($property) {

			return [ $property ];

		}, explode(' ', 'is_utc as_date quarter week year_day weekday tomorrow yesterday utc local'));
	}

	/**
	 * @expectedException \ICanBoogie\PropertyNotDefined
	 */
	public function test_setting_undefined_property_should_throw_exception()
	{
		$d = new MutableDateTime;
		$property = uniqid();
		$d->$property = uniqid();
	}

	/**
	 * @dataProvider provide_test_should_be_same_class
	 *
	 * @param string $property
	 */
	public function test_should_be_same_class($property)
	{
		$this->assertInstanceOf(MutableDateTime::class, MutableDateTime::now()->$property);
	}

	/**
	 * @return array
	 */
	public function provide_test_should_be_same_class()
	{
		return array_map(function ($class) {

			return [ $class ];

		}, explode(' ', 'utc local'));
	}
}
