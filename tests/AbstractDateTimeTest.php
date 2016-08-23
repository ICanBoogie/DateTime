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

abstract class AbstractDateTimeTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @return DateTime|MutableDateTime
	 */
	abstract protected function now();

	/**
	 * @return DateTime|MutableDateTime
	 */
	abstract protected function right_now();

	/**
	 * @param \DateTimeZone|string|null $timezone
	 *
	 * @return DateTime|MutableDateTime
	 */
	abstract protected function none($timezone = 'utc');

	/**
	 * @param mixed $source
	 * @param \DateTimeZone|string|null $timezone
	 *
	 * @return DateTime|MutableDateTime
	 */
	abstract protected function from($source, $timezone = null);

	/**
	 * @param string $time
	 * @param \DateTimeZone|string|null $timezone
	 *
	 * @return DateTime|MutableDateTime
	 */
	abstract protected function create($time = 'now', $timezone = null);

	public function setUp()
	{
		date_default_timezone_set('Europe/Paris');
	}

	public function test_now()
	{
		$d = $this->now();
		$now = new \DateTime('@' . $_SERVER['REQUEST_TIME']);
		$now->setTimezone(new \DateTimeZone(date_default_timezone_get()));
		$this->assertEquals(date_default_timezone_get(), $d->zone->name);
		$this->assertEquals($d->year, $now->format('Y'));
		$this->assertEquals($d->month, $now->format('m'));
		$this->assertEquals($d->day, $now->format('d'));
		$this->assertEquals($d->hour, $now->format('H'));
		$this->assertEquals($d->minute, $now->format('i'));
		$this->assertEquals($d->second, $now->format('s'));

		sleep(2);

		$this->assertEquals($d, $this->now());
		$this->assertGreaterThan($d, DateTime::right_now());
	}

	public function test_none()
	{
		$d = $this->none();
		$this->assertEquals('UTC', $d->zone->name);
		$this->assertEquals(-1, $d->year);
		$this->assertEquals(11, $d->month);
		$this->assertEquals(30, $d->day);
		$this->assertSame("", (string) $d);

		$d = $this->none('Asia/Tokyo');
		$this->assertEquals('Asia/Tokyo', $d->zone->name);
		$this->assertTrue($d->is_empty);
		$this->assertSame("", (string) $d);

		$d = $this->none(new \DateTimeZone('Asia/Tokyo'));
		$this->assertEquals('Asia/Tokyo', $d->zone->name);
		$this->assertTrue($d->is_empty);
	}

	public function test_from()
	{
		$d = $this->from(new \DateTime('2001-01-01 01:01:01', new \DateTimeZone('Europe/Paris')));
		$this->assertEquals('Europe/Paris', $d->zone->name);
		$this->assertEquals('2001-01-01 01:01:01', $d->as_db);

		$d = $this->from(new \DateTime('2001-01-01 01:01:01', new \DateTimeZone('Europe/Paris')), new \DateTimeZone('UTC'));
		$this->assertEquals('Europe/Paris', $d->zone->name);
		$this->assertEquals('2001-01-01 01:01:01', $d->as_db);

		$d = $this->from(new \DateTime('2001-01-01 01:01:01', new \DateTimeZone('UTC')));
		$this->assertEquals('UTC', $d->zone->name);
		$this->assertEquals('2001-01-01 01:01:01', $d->as_db);

		$d = $this->from(new \DateTime('2001-01-01 01:01:01', new \DateTimeZone('UTC')), new \DateTimeZone('Europe/Paris'));
		$this->assertEquals('UTC', $d->zone->name);
		$this->assertEquals('2001-01-01 01:01:01', $d->as_db);

		$d = $this->from('2001-01-01 01:01:01', new \DateTimeZone('UTC'));
		$this->assertEquals('UTC', (string) $d->zone);
		$this->assertEquals('2001-01-01 01:01:01', $d->as_db);

		$d = $this->from('2001-01-01 01:01:01', new \DateTimeZone('Europe/Paris'));
		$this->assertEquals('Europe/Paris', $d->zone->name);
		$this->assertEquals('2001-01-01 01:01:01', $d->as_db);

		$d = $this->from('2001-01-01 01:01:01');
		$this->assertEquals(date_default_timezone_get(), $d->zone->name);
		$this->assertEquals('2001-01-01 01:01:01', $d->as_db);
	}

	public function test_change()
	{
		$d = $this->create('2001-01-01 01:01:01');

		$d = $d->change([ 'year' => 2009 ]);
		$this->assertEquals('2009-01-01 01:01:01', $d->as_db);
		$this->assertEquals(2009, $d->year);
		$d = $d->change([ 'month' => 9 ]);
		$this->assertEquals('2009-09-01 01:01:01', $d->as_db);
		$this->assertEquals(9, $d->month);
		$d = $d->change([ 'day' => 9 ]);
		$this->assertEquals('2009-09-09 01:01:01', $d->as_db);
		$this->assertEquals(9, $d->day);
		$d = $d->change([ 'hour' => 9 ]);
		$this->assertEquals('2009-09-09 09:01:01', $d->as_db);
		$this->assertEquals(9, $d->hour);
		$d = $d->change([ 'minute' => 9 ]);
		$this->assertEquals('2009-09-09 09:09:01', $d->as_db);
		$this->assertEquals(9, $d->minute);
		$d = $d->change([ 'second' => 9 ]);
		$this->assertEquals('2009-09-09 09:09:09', $d->as_db);
		$this->assertEquals(9, $d->second);

		$d = $d->change([

			'year' => 2001,
			'month' => 1,
			'day' => 1,
			'hour' => 1,
			'minute' => 1,
			'second' => 1

		]);

		$this->assertEquals('2001-01-01 01:01:01', $d->as_db);
	}

	public function test_change_cascade()
	{
		$d = $this->create('2001-01-01 01:01:01');
		$this->assertEquals('2001-01-01 01:02:00', $d->change([ 'minute' => 2 ], true)->as_db);
		$d = $this->create('2001-01-01 01:01:01');
		$this->assertEquals('2001-01-01 02:00:00', $d->change([ 'hour' => 2 ], true)->as_db);
		$d = $this->create('2001-01-01 01:01:01');
		$this->assertEquals('2001-01-01 01:02:02', $d->change([ 'minute' => 2, 'second' => 2 ], true)->as_db);
		$d = $this->create('2001-01-01 01:01:01');
		$this->assertEquals('2001-01-01 02:02:00', $d->change([ 'hour' => 2, 'minute' => 2 ], true)->as_db);
		$d = $this->create('2001-01-01 01:01:01');
		$this->assertEquals('2001-01-01 02:02:02', $d->change([ 'hour' => 2, 'minute' => 2, 'second' => 2 ], true)->as_db);
		# check fix: zero values don't cascade
		$d = $this->create('2001-01-01 01:01:01');
		$this->assertEquals('2001-01-01 01:00:00', $d->change([ 'minute' => 0 ], true)->as_db);
		$d = $this->create('2001-01-01 01:01:01');
		$this->assertEquals('2001-01-01 00:00:00', $d->change([ 'hour' => 0 ], true)->as_db);
	}

	public function test_with()
	{
		$d = $this->create('2001-01-01 01:01:01');
		$e = $d->with([ 'year' => 2015, 'month' => 5, 'day' => 5 ]);

		$this->assertNotSame($d, $e);
		$this->assertEquals(2001, $d->year);
		$this->assertEquals(2015, $e->year);
		$this->assertEquals('2001-01-01 01:01:01', $d->as_db);
		$this->assertEquals('2015-05-05 01:01:01', $e->as_db);
	}

	public function test_get_year()
	{
		$d = $this->create('2012-12-16 15:00:00');
		$this->assertEquals(2012, $d->year);
		$d = $this->create('0000-12-16 15:00:00');
		$this->assertEquals(0, $d->year);
		$d = $this->create('9999-12-16 15:00:00');
		$this->assertEquals(9999, $d->year);
	}

	public function test_get_quarter()
	{
		$d = $this->create('2012-01-16 15:00:00');
		$this->assertEquals(1, $d->quarter);
		$d = $this->create('2012-02-16 15:00:00');
		$this->assertEquals(1, $d->quarter);
		$d = $this->create('2012-03-16 15:00:00');
		$this->assertEquals(1, $d->quarter);
		$d = $this->create('2012-04-16 15:00:00');
		$this->assertEquals(2, $d->quarter);
		$d = $this->create('2012-05-16 15:00:00');
		$this->assertEquals(2, $d->quarter);
		$d = $this->create('2012-06-16 15:00:00');
		$this->assertEquals(2, $d->quarter);
		$d = $this->create('2012-07-16 15:00:00');
		$this->assertEquals(3, $d->quarter);
		$d = $this->create('2012-08-16 15:00:00');
		$this->assertEquals(3, $d->quarter);
		$d = $this->create('2012-09-16 15:00:00');
		$this->assertEquals(3, $d->quarter);
		$d = $this->create('2012-10-16 15:00:00');
		$this->assertEquals(4, $d->quarter);
		$d = $this->create('2012-11-16 15:00:00');
		$this->assertEquals(4, $d->quarter);
		$d = $this->create('2012-12-16 15:00:00');
		$this->assertEquals(4, $d->quarter);
	}

	/**
	 * @expectedException \ICanBoogie\PropertyNotWritable
	 */
	public function test_set_quarter()
	{
		$d = $this->now();
		$d->quarter = true;
	}

	public function test_get_month()
	{
		$d = $this->create('2012-01-16 15:00:00');
		$this->assertEquals(1, $d->month);
		$d = $this->create('2012-06-16 15:00:00');
		$this->assertEquals(6, $d->month);
		$d = $this->create('2012-12-16 15:00:00');
		$this->assertEquals(12, $d->month);
	}

	public function test_get_week()
	{
		$d = $this->create('2012-01-01 15:00:00');
		$this->assertEquals(52, $d->week);
		$d = $this->create('2012-01-16 15:00:00');
		$this->assertEquals(3, $d->week);
	}

	/**
	 * @expectedException \ICanBoogie\PropertyNotWritable
	 */
	public function test_set_week()
	{
		$d = $this->now();
		$d->week = true;
	}

	public function test_get_year_day()
	{
		$d = $this->create('2012-01-01 15:00:00');
		$this->assertEquals(1, $d->year_day);
		$d = $this->create('2012-12-31 15:00:00');
		$this->assertEquals(366, $d->year_day);
	}

	/**
	 * Sunday must be 7, Monday must be 1.
	 */
	public function test_get_weekday()
	{
		$d = $this->create('2012-12-17 15:00:00');
		$this->assertEquals(1, $d->weekday);
		$d = $this->create('2012-12-18 15:00:00');
		$this->assertEquals(2, $d->weekday);
		$d = $this->create('2012-12-19 15:00:00');
		$this->assertEquals(3, $d->weekday);
		$d = $this->create('2012-12-20 15:00:00');
		$this->assertEquals(4, $d->weekday);
		$d = $this->create('2012-12-21 15:00:00');
		$this->assertEquals(5, $d->weekday);
		$d = $this->create('2012-12-22 15:00:00');
		$this->assertEquals(6, $d->weekday);
		$d = $this->create('2012-12-23 15:00:00');
		$this->assertEquals(7, $d->weekday);
	}

	/**
	 * @expectedException \ICanBoogie\PropertyNotWritable
	 */
	public function test_set_weekday()
	{
		$d = $this->now();
		$d->weekday = true;
	}

	public function test_get_day()
	{
		$d = $this->create('2012-12-16 15:00:00');
		$this->assertEquals(16, $d->day);
		$d = $this->create('2012-12-17 15:00:00');
		$this->assertEquals(17, $d->day);
		$d = $this->create('2013-01-01 03:00:00');
		$this->assertEquals(1, $d->day);
	}

	public function test_get_hour()
	{
		$d = $this->create('2013-01-01 01:23:45');
		$this->assertEquals(1, $d->hour);
	}

	public function test_get_minute()
	{
		$d = $this->create('2013-01-01 01:23:45');
		$this->assertEquals(23, $d->minute);
	}

	public function test_get_second()
	{
		$d = $this->create('2013-01-01 01:23:45');
		$this->assertEquals(45, $d->second);
	}

	public function test_get_is_monday()
	{
		$d = $this->create('2013-02-04 21:00:00', 'utc');
		$this->assertTrue($d->is_monday);
		$this->assertFalse($d->is_tuesday);
		$this->assertFalse($d->is_wednesday);
		$this->assertFalse($d->is_thursday);
		$this->assertFalse($d->is_friday);
		$this->assertFalse($d->is_saturday);
		$this->assertFalse($d->is_sunday);
	}

	public function test_get_is_tuesday()
	{
		$d = $this->create('2013-02-05 21:00:00', 'utc');
		$this->assertFalse($d->is_monday);
		$this->assertTrue($d->is_tuesday);
		$this->assertFalse($d->is_wednesday);
		$this->assertFalse($d->is_thursday);
		$this->assertFalse($d->is_friday);
		$this->assertFalse($d->is_saturday);
		$this->assertFalse($d->is_sunday);
	}

	public function test_get_is_wednesday()
	{
		$d = $this->create('2013-02-06 21:00:00', 'utc');
		$this->assertFalse($d->is_monday);
		$this->assertFalse($d->is_tuesday);
		$this->assertTrue($d->is_wednesday);
		$this->assertFalse($d->is_thursday);
		$this->assertFalse($d->is_friday);
		$this->assertFalse($d->is_saturday);
		$this->assertFalse($d->is_sunday);
	}

	public function test_get_is_thursday()
	{
		$d = $this->create('2013-02-07 21:00:00', 'utc');
		$this->assertFalse($d->is_monday);
		$this->assertFalse($d->is_tuesday);
		$this->assertFalse($d->is_wednesday);
		$this->assertTrue($d->is_thursday);
		$this->assertFalse($d->is_friday);
		$this->assertFalse($d->is_saturday);
		$this->assertFalse($d->is_sunday);
	}

	public function test_get_is_friday()
	{
		$d = $this->create('2013-02-08 21:00:00', 'utc');
		$this->assertFalse($d->is_monday);
		$this->assertFalse($d->is_tuesday);
		$this->assertFalse($d->is_wednesday);
		$this->assertFalse($d->is_thursday);
		$this->assertTrue($d->is_friday);
		$this->assertFalse($d->is_saturday);
		$this->assertFalse($d->is_sunday);
	}

	public function test_get_is_saturday()
	{
		$d = $this->create('2013-02-09 21:00:00', 'utc');
		$this->assertFalse($d->is_monday);
		$this->assertFalse($d->is_tuesday);
		$this->assertFalse($d->is_wednesday);
		$this->assertFalse($d->is_thursday);
		$this->assertFalse($d->is_friday);
		$this->assertTrue($d->is_saturday);
		$this->assertFalse($d->is_sunday);
	}

	public function test_get_is_sunday()
	{
		$d = $this->create('2013-02-10 21:00:00', 'utc');
		$this->assertFalse($d->is_monday);
		$this->assertFalse($d->is_tuesday);
		$this->assertFalse($d->is_wednesday);
		$this->assertFalse($d->is_thursday);
		$this->assertFalse($d->is_friday);
		$this->assertFalse($d->is_saturday);
		$this->assertTrue($d->is_sunday);
	}

	public function test_get_is_today()
	{
		$d = $this->now()->setTimezone('Asia/Tokyo');
		$this->assertTrue($d->is_today);
		$this->assertFalse($d->tomorrow->is_today);
		$this->assertFalse($d->yesterday->is_today);
	}

	public function test_get_is_empty()
	{
		$d = $this->none();
		$this->assertTrue($d->is_empty);
		$d = $this->create('0000-00-00 00:00:00');
		$this->assertTrue($d->is_empty);
		$d = $this->create('0000-00-00');
		$this->assertTrue($d->is_empty);
		$d = $this->create('now');
		$this->assertFalse($d->is_empty);
		$d = $this->create('@0');
		$this->assertFalse($d->is_empty);
	}

	public function test_get_tomorrow()
	{
		$d = $this->create('2013-02-10 21:21:21', 'utc');
		$this->assertEquals('2013-02-11 00:00:00', $d->tomorrow->as_db);
	}

	public function test_get_yesterday()
	{
		$d = $this->create('2013-02-10 21:21:21', 'utc');
		$this->assertEquals('2013-02-09 00:00:00', $d->yesterday->as_db);
	}

	/**
	 * @dataProvider provide_test_day_instance
	 *
	 * @param string $date
	 * @param string $expected
	 * @param string $day
	 */
	public function test_day_instance($date, $expected, $day)
	{
		$d = $this->create($date);
		$this->assertEquals($expected, $d->$day->as_db);
	}

	/**
	 * @return array
	 */
	public function provide_test_day_instance()
	{
		return [

			# monday
			[ '2014-01-06 00:00:00', '2014-01-06 00:00:00', 'monday' ], // Mo
			[ '2014-01-06 11:11:11', '2014-01-06 00:00:00', 'monday' ], // Mo
			[ '2014-01-07 00:00:00', '2014-01-06 00:00:00', 'monday' ], // Tu
			[ '2014-01-07 11:11:11', '2014-01-06 00:00:00', 'monday' ], // Tu
			[ '2014-01-08 00:00:00', '2014-01-06 00:00:00', 'monday' ], // We
			[ '2014-01-08 11:11:11', '2014-01-06 00:00:00', 'monday' ], // We
			[ '2014-01-09 00:00:00', '2014-01-06 00:00:00', 'monday' ], // Th
			[ '2014-01-09 11:11:11', '2014-01-06 00:00:00', 'monday' ], // Th
			[ '2014-01-10 00:00:00', '2014-01-06 00:00:00', 'monday' ], // Fr
			[ '2014-01-10 11:11:11', '2014-01-06 00:00:00', 'monday' ], // Fr
			[ '2014-01-11 00:00:00', '2014-01-06 00:00:00', 'monday' ], // Sa
			[ '2014-01-11 11:11:11', '2014-01-06 00:00:00', 'monday' ], // Sa
			[ '2014-01-12 00:00:00', '2014-01-06 00:00:00', 'monday' ], // Su
			[ '2014-01-12 11:11:11', '2014-01-06 00:00:00', 'monday' ], // Su

			# tuesday
			[ '2014-01-06 00:00:00', '2014-01-07 00:00:00', 'tuesday' ], // Mo
			[ '2014-01-06 11:11:11', '2014-01-07 00:00:00', 'tuesday' ], // Mo
			[ '2014-01-07 00:00:00', '2014-01-07 00:00:00', 'tuesday' ], // Tu
			[ '2014-01-07 11:11:11', '2014-01-07 00:00:00', 'tuesday' ], // Tu
			[ '2014-01-08 00:00:00', '2014-01-07 00:00:00', 'tuesday' ], // We
			[ '2014-01-08 11:11:11', '2014-01-07 00:00:00', 'tuesday' ], // We
			[ '2014-01-09 00:00:00', '2014-01-07 00:00:00', 'tuesday' ], // Th
			[ '2014-01-09 11:11:11', '2014-01-07 00:00:00', 'tuesday' ], // Th
			[ '2014-01-10 00:00:00', '2014-01-07 00:00:00', 'tuesday' ], // Fr
			[ '2014-01-10 11:11:11', '2014-01-07 00:00:00', 'tuesday' ], // Fr
			[ '2014-01-11 00:00:00', '2014-01-07 00:00:00', 'tuesday' ], // Sa
			[ '2014-01-11 11:11:11', '2014-01-07 00:00:00', 'tuesday' ], // Sa
			[ '2014-01-12 00:00:00', '2014-01-07 00:00:00', 'tuesday' ], // Su
			[ '2014-01-12 11:11:11', '2014-01-07 00:00:00', 'tuesday' ], // Su

			# wednesday
			[ '2014-01-06 00:00:00', '2014-01-08 00:00:00', 'wednesday' ], // Mo
			[ '2014-01-06 11:11:11', '2014-01-08 00:00:00', 'wednesday' ], // Mo
			[ '2014-01-07 00:00:00', '2014-01-08 00:00:00', 'wednesday' ], // Tu
			[ '2014-01-07 11:11:11', '2014-01-08 00:00:00', 'wednesday' ], // Tu
			[ '2014-01-08 00:00:00', '2014-01-08 00:00:00', 'wednesday' ], // We
			[ '2014-01-08 11:11:11', '2014-01-08 00:00:00', 'wednesday' ], // We
			[ '2014-01-09 00:00:00', '2014-01-08 00:00:00', 'wednesday' ], // Th
			[ '2014-01-09 11:11:11', '2014-01-08 00:00:00', 'wednesday' ], // Th
			[ '2014-01-10 00:00:00', '2014-01-08 00:00:00', 'wednesday' ], // Fr
			[ '2014-01-10 11:11:11', '2014-01-08 00:00:00', 'wednesday' ], // Fr
			[ '2014-01-11 00:00:00', '2014-01-08 00:00:00', 'wednesday' ], // Sa
			[ '2014-01-11 11:11:11', '2014-01-08 00:00:00', 'wednesday' ], // Sa
			[ '2014-01-12 00:00:00', '2014-01-08 00:00:00', 'wednesday' ], // Su
			[ '2014-01-12 11:11:11', '2014-01-08 00:00:00', 'wednesday' ], // Su

			# thursday
			[ '2014-01-06 00:00:00', '2014-01-09 00:00:00', 'thursday' ], // Mo
			[ '2014-01-06 11:11:11', '2014-01-09 00:00:00', 'thursday' ], // Mo
			[ '2014-01-07 00:00:00', '2014-01-09 00:00:00', 'thursday' ], // Tu
			[ '2014-01-07 11:11:11', '2014-01-09 00:00:00', 'thursday' ], // Tu
			[ '2014-01-08 00:00:00', '2014-01-09 00:00:00', 'thursday' ], // We
			[ '2014-01-08 11:11:11', '2014-01-09 00:00:00', 'thursday' ], // We
			[ '2014-01-09 00:00:00', '2014-01-09 00:00:00', 'thursday' ], // Th
			[ '2014-01-09 11:11:11', '2014-01-09 00:00:00', 'thursday' ], // Th
			[ '2014-01-10 00:00:00', '2014-01-09 00:00:00', 'thursday' ], // Fr
			[ '2014-01-10 11:11:11', '2014-01-09 00:00:00', 'thursday' ], // Fr
			[ '2014-01-11 00:00:00', '2014-01-09 00:00:00', 'thursday' ], // Sa
			[ '2014-01-11 11:11:11', '2014-01-09 00:00:00', 'thursday' ], // Sa
			[ '2014-01-12 00:00:00', '2014-01-09 00:00:00', 'thursday' ], // Su
			[ '2014-01-12 11:11:11', '2014-01-09 00:00:00', 'thursday' ], // Su

			# friday
			[ '2014-01-06 00:00:00', '2014-01-10 00:00:00', 'friday' ], // Mo
			[ '2014-01-06 11:11:11', '2014-01-10 00:00:00', 'friday' ], // Mo
			[ '2014-01-07 00:00:00', '2014-01-10 00:00:00', 'friday' ], // Tu
			[ '2014-01-07 11:11:11', '2014-01-10 00:00:00', 'friday' ], // Tu
			[ '2014-01-08 00:00:00', '2014-01-10 00:00:00', 'friday' ], // We
			[ '2014-01-08 11:11:11', '2014-01-10 00:00:00', 'friday' ], // We
			[ '2014-01-09 00:00:00', '2014-01-10 00:00:00', 'friday' ], // Th
			[ '2014-01-09 11:11:11', '2014-01-10 00:00:00', 'friday' ], // Th
			[ '2014-01-10 00:00:00', '2014-01-10 00:00:00', 'friday' ], // Fr
			[ '2014-01-10 11:11:11', '2014-01-10 00:00:00', 'friday' ], // Fr
			[ '2014-01-11 00:00:00', '2014-01-10 00:00:00', 'friday' ], // Sa
			[ '2014-01-11 11:11:11', '2014-01-10 00:00:00', 'friday' ], // Sa
			[ '2014-01-12 00:00:00', '2014-01-10 00:00:00', 'friday' ], // Su
			[ '2014-01-12 11:11:11', '2014-01-10 00:00:00', 'friday' ], // Su

			# saturday
			[ '2014-01-06 00:00:00', '2014-01-11 00:00:00', 'saturday' ], // Mo
			[ '2014-01-06 11:11:11', '2014-01-11 00:00:00', 'saturday' ], // Mo
			[ '2014-01-07 00:00:00', '2014-01-11 00:00:00', 'saturday' ], // Tu
			[ '2014-01-07 11:11:11', '2014-01-11 00:00:00', 'saturday' ], // Tu
			[ '2014-01-08 00:00:00', '2014-01-11 00:00:00', 'saturday' ], // We
			[ '2014-01-08 11:11:11', '2014-01-11 00:00:00', 'saturday' ], // We
			[ '2014-01-09 00:00:00', '2014-01-11 00:00:00', 'saturday' ], // Th
			[ '2014-01-09 11:11:11', '2014-01-11 00:00:00', 'saturday' ], // Th
			[ '2014-01-10 00:00:00', '2014-01-11 00:00:00', 'saturday' ], // Fr
			[ '2014-01-10 11:11:11', '2014-01-11 00:00:00', 'saturday' ], // Fr
			[ '2014-01-11 00:00:00', '2014-01-11 00:00:00', 'saturday' ], // Sa
			[ '2014-01-11 11:11:11', '2014-01-11 00:00:00', 'saturday' ], // Sa
			[ '2014-01-12 00:00:00', '2014-01-11 00:00:00', 'saturday' ], // Su
			[ '2014-01-12 11:11:11', '2014-01-11 00:00:00', 'saturday' ], // Su

			# sunday
			[ '2014-01-06 00:00:00', '2014-01-12 00:00:00', 'sunday' ], // Mo
			[ '2014-01-06 11:11:11', '2014-01-12 00:00:00', 'sunday' ], // Mo
			[ '2014-01-07 00:00:00', '2014-01-12 00:00:00', 'sunday' ], // Tu
			[ '2014-01-07 11:11:11', '2014-01-12 00:00:00', 'sunday' ], // Tu
			[ '2014-01-08 00:00:00', '2014-01-12 00:00:00', 'sunday' ], // We
			[ '2014-01-08 11:11:11', '2014-01-12 00:00:00', 'sunday' ], // We
			[ '2014-01-09 00:00:00', '2014-01-12 00:00:00', 'sunday' ], // Th
			[ '2014-01-09 11:11:11', '2014-01-12 00:00:00', 'sunday' ], // Th
			[ '2014-01-10 00:00:00', '2014-01-12 00:00:00', 'sunday' ], // Fr
			[ '2014-01-10 11:11:11', '2014-01-12 00:00:00', 'sunday' ], // Fr
			[ '2014-01-11 00:00:00', '2014-01-12 00:00:00', 'sunday' ], // Sa
			[ '2014-01-11 11:11:11', '2014-01-12 00:00:00', 'sunday' ], // Sa
			[ '2014-01-12 00:00:00', '2014-01-12 00:00:00', 'sunday' ], // Su
			[ '2014-01-12 11:11:11', '2014-01-12 00:00:00', 'sunday' ], // Su

		];
	}

	public function test_far_past()
	{
		$d = $this->create('-4712-12-07 12:06:46');
		$this->assertEquals(-4712, $d->year);
		$this->assertEquals('-4712-12-07 12:06:46', $d->as_db);
	}

	public function test_far_future()
	{
		$d = $this->create('4712-12-07 12:06:46');
		$this->assertEquals(4712, $d->year);
		$this->assertEquals('4712-12-07 12:06:46', $d->as_db);
	}

	public function test_get_utc()
	{
		$d = $this->create('2013-03-06 18:00:00', 'Europe/Paris');
		$utc = $d->utc;

		$this->assertEquals('UTC', $utc->zone->name);
		$this->assertEquals('2013-03-06 17:00:00', $utc->as_db);
	}

	/**
	 * @expectedException \ICanBoogie\PropertyNotWritable
	 */
	public function test_set_utc()
	{
		$d = $this->now();
		$d->utc = null;
	}

	public function test_get_is_utc()
	{
		$d = $this->now()->setTimezone('Asia/Tokyo');
		$this->assertFalse($d->is_utc);
		$this->assertTrue($d->setTimezone('UTC')->is_utc);
	}

	public function test_get_local()
	{
		$d = $this->create('2013-03-06 17:00:00', 'UTC');
		$local = $d->local;

		$this->assertEquals('Europe/Paris', $local->zone->name);
		$this->assertEquals('2013-03-06 18:00:00', $local->as_db);
	}

	/**
	 * @expectedException \ICanBoogie\PropertyNotWritable
	 */
	public function test_set_local()
	{
		$d = $this->now();
		$d->local = true;
	}

	public function test_get_is_local()
	{
		$d = $this->now();
		$this->assertFalse($d->setTimezone(date_default_timezone_get() == 'UTC' ? 'Asia/Tokyo' : 'UTC')->is_local);
		$this->assertTrue($d->setTimezone(date_default_timezone_get())->is_local);
	}

	public function test_get_is_dst()
	{
		$d = $this->create('2013-02-03 21:00:00');
		$this->assertFalse($d->is_dst);
		$d = $this->create('2013-08-03 21:00:00');
		$this->assertTrue($d->is_dst);
	}

	public function test_format()
	{
		$empty = $this->none();
		$this->assertEquals('0000-00-00', $empty->format(DateTime::DATE));
		$this->assertEquals('0000-00-00 00:00:00', $empty->format(DateTime::DB));
		$this->assertStringEndsWith('30 Nov -0001 00:00:00 +0000', $empty->format(DateTime::RSS));
	}

	/*
	 * Predefined formats
	 */

	public function test_format_as_atom()
	{
		$now = $this->now();
		$this->assertEquals($now->format(DateTime::ATOM), $now->format_as_atom());
	}

	public function test_get_as_atom()
	{
		$now = $this->now();
		$this->assertEquals($now->format(DateTime::ATOM), $now->as_atom);
	}

	public function test_format_as_cookie()
	{
		$now = $this->now();
		$this->assertEquals($now->format(DateTime::COOKIE), $now->format_as_cookie());
	}

	public function test_get_as_cookie()
	{
		$now = $this->now();
		$this->assertEquals($now->format(DateTime::COOKIE), $now->as_cookie);

		$date = $this->create('2013-11-04 20:21:22 UTC');
		$this->assertEquals("Monday, 04-Nov-2013 20:21:22 UTC", $date->as_cookie);
	}

	public function test_format_as_iso8601()
	{
		$now = $this->now();
		$this->assertEquals($now->format(DateTime::ISO8601), $now->format_as_iso8601());
	}

	public function test_format_as_iso8601_utc()
	{
		$now = $this->now()->utc;
		$this->assertEquals(str_replace('+0000', 'Z', $now->format(DateTime::ISO8601)), $now->format_as_iso8601());
	}

	public function test_get_as_iso8601()
	{
		$now = $this->now();
		$this->assertEquals($now->format(DateTime::ISO8601), $now->as_iso8601);
	}

	public function test_as_iso8601_utc()
	{
		$now = $this->now()->utc;
		$this->assertEquals(str_replace('+0000', 'Z', $now->format(DateTime::ISO8601)), $now->as_iso8601);
	}

	public function test_format_as_rfc822()
	{
		$now = $this->now();
		$this->assertEquals($now->format(DateTime::RFC822), $now->format_as_rfc822());
	}

	public function test_format_as_rfc822_utc()
	{
		$now = $this->now()->utc;
		$this->assertEquals(str_replace('+0000', 'GMT', $now->format(DateTime::RFC822)), $now->format_as_rfc822());
	}

	public function test_get_as_rfc822()
	{
		$now = $this->now();
		$this->assertEquals($now->format(DateTime::RFC822), $now->as_rfc822);
	}

	public function test_get_as_rfc822_utc()
	{
		$now = $this->now()->utc;
		$this->assertEquals(str_replace('+0000', 'GMT', $now->format(DateTime::RFC822)), $now->as_rfc822);
	}

	public function test_format_as_rfc850()
	{
		$now = $this->now();
		$this->assertEquals($now->format(DateTime::RFC850), $now->format_as_rfc850());
	}

	public function test_get_as_rfc850()
	{
		$now = $this->now();
		$this->assertEquals($now->format(DateTime::RFC850), $now->as_rfc850);
	}

	public function test_format_as_rfc1036()
	{
		$now = $this->now();
		$this->assertEquals($now->format(DateTime::RFC1036), $now->format_as_rfc1036());
	}

	public function test_get_as_rfc1036()
	{
		$now = $this->now();
		$this->assertEquals($now->format(DateTime::RFC1036), $now->as_rfc1036);
	}

	public function test_format_as_rfc1123()
	{
		$now = $this->now();
		$this->assertEquals($now->format(DateTime::RFC1123), $now->format_as_rfc1123());
	}

	public function test_format_as_rfc1123_utc()
	{
		$now = $this->now()->utc;
		$this->assertEquals(str_replace('+0000', 'GMT', $now->format(DateTime::RFC1123)), $now->format_as_rfc1123());
	}

	public function test_get_as_rfc1123()
	{
		$now = $this->now();
		$this->assertEquals($now->format(DateTime::RFC1123), $now->as_rfc1123);
	}

	public function test_get_as_rfc1123_utc()
	{
		$now = $this->now()->utc;
		$this->assertEquals(str_replace('+0000', 'GMT', $now->format(DateTime::RFC1123)), $now->as_rfc1123);
	}

	public function test_format_as_rfc2822()
	{
		$now = $this->now();
		$this->assertEquals($now->format(DateTime::RFC2822), $now->format_as_rfc2822());
	}

	public function test_get_as_rfc2822()
	{
		$now = $this->now();
		$this->assertEquals($now->format(DateTime::RFC2822), $now->as_rfc2822);
	}

	public function test_format_as_rfc3339()
	{
		$now = $this->now();
		$this->assertEquals($now->format(DateTime::RFC3339), $now->format_as_rfc3339());
	}

	public function test_get_as_rfc3339()
	{
		$now = $this->now();
		$this->assertEquals($now->format(DateTime::RFC3339), $now->as_rfc3339);
	}

	public function test_format_as_rss()
	{
		$now = $this->now();
		$this->assertEquals($now->format(DateTime::RSS), $now->format_as_rss());
	}

	public function test_get_as_rss()
	{
		$now = $this->now();
		$this->assertEquals($now->format(DateTime::RSS), $now->as_rss);
	}

	public function test_format_as_w3c()
	{
		$now = $this->now();
		$this->assertEquals($now->format(DateTime::W3C), $now->format_as_w3c());
	}

	public function test_get_as_w3c()
	{
		$now = $this->now();
		$this->assertEquals($now->format(DateTime::W3C), $now->as_w3c);
	}

	public function test_format_as_db()
	{
		$now = $this->now();
		$this->assertEquals($now->format(DateTime::DB), $now->format_as_db());
		$empty = $this->none();
		$this->assertEquals('0000-00-00 00:00:00', $empty->format_as_db());
	}

	public function test_get_as_db()
	{
		$now = $this->now();
		$this->assertEquals($now->format(DateTime::DB), $now->as_db);
		$empty = $this->none();
		$this->assertEquals('0000-00-00 00:00:00', $empty->as_db);
	}

	public function test_format_as_number()
	{
		$now = $this->now();
		$this->assertEquals($now->format(DateTime::NUMBER), $now->format_as_number());
	}

	public function test_get_as_number()
	{
		$now = $this->now();
		$this->assertEquals($now->format(DateTime::NUMBER), $now->as_number);
	}

	public function test_format_as_date()
	{
		$now = $this->now();
		$this->assertEquals($now->format(DateTime::DATE), $now->format_as_date());
		$empty = $this->none();
		$this->assertEquals('0000-00-00', $empty->format_as_date());
	}

	public function test_get_as_date()
	{
		$now = $this->now();
		$this->assertEquals($now->format(DateTime::DATE), $now->as_date);
		$empty = $this->none();
		$this->assertEquals('0000-00-00', $empty->as_date);
	}

	public function test_format_as_time()
	{
		$now = $this->now();
		$this->assertEquals($now->format(DateTime::TIME), $now->format_as_time());
	}

	public function test_get_as_time()
	{
		$now = $this->now();
		$this->assertEquals($now->format(DateTime::TIME), $now->as_time);
	}

	public function test_compare()
	{
		$d1 = $this->now();
		$d2 = MutableDateTime::now();

		$this->assertTrue($d1 == $d2);
		$this->assertTrue($d1 >= $d2);
		$this->assertTrue($d1 <= $d2);
		$this->assertFalse($d1 != $d2);
		$this->assertFalse($d1 > $d2);
		$this->assertFalse($d1 < $d2);

		$d2->second++;
		$this->assertTrue($d1 != $d2);
		$this->assertTrue($d1 < $d2);
		$this->assertTrue($d2 > $d1);
		$this->assertFalse($d1 == $d2);
		$this->assertFalse($d1 >= $d2);
		$this->assertFalse($d2 <= $d1);

		$now = $this->now();
		$yesterday = $now->yesterday;
		$tomorrow = $now->tomorrow;

		$this->assertTrue($now > $yesterday && $now < $tomorrow);
		$this->assertSame($yesterday, min($now, $yesterday, $tomorrow));
		$this->assertSame($tomorrow, max($now, $yesterday, $tomorrow));
	}

	public function test_json_serialize()
	{
		$date = $this->create("2014-10-23 13:50:10", "Europe/Paris");
		$this->assertEquals('{"date":"2014-10-23T13:50:10+0200"}', json_encode([ 'date' => $date ]));
	}

	/**
	 * @expectedException \LogicException
	 */
	public function test_localize_should_throw_an_exception_if_the_localizer_is_not_defined_yet()
	{
		DateTime::$localizer = null;

		$this->now()->localize('fr');
	}

	public function test_localize()
	{
		$invoked = false;
		$reference = $this->now();

		DateTime::$localizer = function(\DateTimeInterface $datetime, $locale) use (&$invoked, &$reference) {

			$this->assertSame($datetime, $reference);
			$this->assertEquals('fr', $locale);

		};

		$reference->localize('fr');
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function test_getting_undefined_property_should_throw_exception()
	{
		$date = $this->now();
		$property = uniqid();
		$date->$property;
	}

	/**
	 * @expectedException \BadMethodCallException
	 */
	public function test_calling_undefined_method_should_throw_exception()
	{
		$date = $this->now();
		$method = uniqid();
		$date->$method();
	}

	/**
	 * @dataProvider provide_read_only_properties
	 * @expectedException \ICanBoogie\PropertyNotWritable
	 *
	 * @param $property
	 */
	public function test_setting_read_only_property_should_throw_exception($property)
	{
		$date = $this->create();
		$date->$property = uniqid();
	}

	/**
	 * @return array
	 */
	public function provide_read_only_properties()
	{
		return array_map(function($property) {

			return [ $property ];

		}, explode(' ', 'quarter week weekday year_day is_monday is_tuesday is_wednesday is_thursday is_friday is_saturday is_sunday is_today is_past is_future is_empty tomorrow yesterday monday tuesday wednesday thursday friday saturday sunday is_utc is_local is_dst as_atom as_cookie as_iso8601 as_rfc822 as_rfc850 as_rfc1036 as_rfc1123 as_rfc2822 as_rfc3339 as_rss as_w3c as_db as_number as_date as_time utc local mutable immutable'));
	}

	public function test_get_mutable()
	{
		$datetime = $this->create();
		$mutable = $datetime->mutable;
		$this->assertNotSame($datetime, $mutable);
		$this->assertInstanceOf(MutableDateTime::class, $mutable);
	}

	public function test_get_immutable()
	{
		$datetime = $this->create();
		$immutable = $datetime->immutable;
		$this->assertNotSame($datetime, $immutable);
		$this->assertInstanceOf(DateTime::class, $immutable);
	}
}
