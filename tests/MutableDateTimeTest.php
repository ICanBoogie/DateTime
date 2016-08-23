<?php

namespace ICanBoogie;

class MutableDateTimeTest extends AbstractDateTimeTest
{
	/**
	 * @inheritdoc
	 *
	 * @return MutableDateTime
	 */
	protected function now()
	{
		return MutableDateTime::now();
	}

	/**
	 * @inheritdoc
	 *
	 * @return MutableDateTime
	 */
	protected function right_now()
	{
		return MutableDateTime::right_now();
	}

	/**
	 * @inheritdoc
	 *
	 * @return MutableDateTime
	 */
	protected function none($timezone = 'utc')
	{
		return MutableDateTime::none($timezone);
	}

	/**
	 * @inheritdoc
	 *
	 * @return MutableDateTime
	 */
	protected function from($source, $timezone = null)
	{
		return MutableDateTime::from($source, $timezone);
	}

	/**
	 * @inheritdoc
	 *
	 * @return MutableDateTime
	 */
	protected function create($time = 'now', $timezone = null)
	{
		return new MutableDateTime($time, $timezone);
	}

	/*
	 * Different expectations
	 */

	public function test_from_instance()
	{
		$d = new \DateTime;
		$this->assertInstanceOf(MutableDateTime::class, $this->from($d));
		$this->assertInstanceOf(ExtendedMutableDateTime::class, ExtendedMutableDateTime::from($d));

		$d = $this->create();
		$this->assertInstanceOf(MutableDateTime::class, $this->from($d));
		$this->assertInstanceOf(ExtendedMutableDateTime::class, ExtendedMutableDateTime::from($d));
	}

	public function test_now_should_provide_a_new_instance()
	{
		$now = $this->now();

		$this->assertNotSame($now, MutableDateTime::now());
	}

	public function test_get_is_past()
	{
		$d = $this->now();
		$d->zone = 'Asia/Tokyo';
		$d->second -= 3600;
		$this->assertTrue($d->is_past);
		$d->second += 7200;
		$this->assertFalse($d->is_past);
	}

	public function test_get_is_future()
	{
		$d = $this->now();
		$d->zone = 'Asia/Tokyo';
		$d->second -= 3600;
		$this->assertFalse($d->is_future);
		$d->second += 7200;
		$this->assertTrue($d->is_future);
	}

	/**
	 * @dataProvider provide_test_should_be_same_class
	 *
	 * @param string $property
	 */
	public function test_should_be_same_class($property)
	{
		$this->assertInstanceOf(MutableDateTime::class, $this->now()->$property);
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

	/*
	 * MutableDateTime additional tests
	 */

	public function test_set_year()
	{
		$d = $this->create('2001-01-01 01:01:01');
		$d->year = 2009;
		$this->assertEquals('2009-01-01 01:01:01', $d->as_db);
	}

	public function test_set_month()
	{
		$d = $this->create('2001-01-01 01:01:01');
		$d->month = 9;
		$this->assertEquals('2001-09-01 01:01:01', $d->as_db);
	}

	public function test_set_day()
	{
		$d = $this->create('2001-01-01 01:01:01');
		$d->day = 9;
		$this->assertEquals('2001-01-09 01:01:01', $d->as_db);
	}

	public function test_set_hour()
	{
		$d = $this->create('2001-01-01 01:01:01');
		$d->hour = 9;
		$this->assertEquals('2001-01-01 09:01:01', $d->as_db);
	}

	public function test_set_minute()
	{
		$d = $this->create('2001-01-01 01:01:01');
		$d->minute = 9;
		$this->assertEquals('2001-01-01 01:09:01', $d->as_db);
	}

	public function test_set_second()
	{
		$d = $this->create('2001-01-01 01:01:01');
		$d->second = 9;
		$this->assertEquals('2001-01-01 01:01:09', $d->as_db);
	}

	public function test_set_timestamp()
	{
		$d = $this->create(null, 'utc');
		$d->timestamp = 234446400;
		$this->assertSame("1977-06-06T12:00:00Z", (string) $d);
	}

	public function test_set_zone()
	{
		$d = $this->create('1977-06-06T12:00:00', 'Asia/Tokyo');
		$this->assertSame('1977-06-06T12:00:00+0900', (string) $d);
		$d->zone = 'Europe/Paris';
		$this->assertSame('1977-06-06T05:00:00+0200', (string) $d);
	}

	/**
	 * @expectedException \LogicException
	 */
	public function test_setting_undefined_property_should_throw_exception()
	{
		$d = new MutableDateTime;
		$property = uniqid();
		$d->$property = uniqid();
	}
}
