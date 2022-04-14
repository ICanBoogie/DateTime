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

use DateTimeInterface;
use DateTimeZone;
use Exception;
use ICanBoogie\DateTime;
use ICanBoogie\PropertyNotWritable;
use PHPUnit\Framework\TestCase;
use function array_map;
use function preg_split;

class DateTimeTest extends TestCase
{
	protected function setUp(): void
	{
		parent::setUp();

		date_default_timezone_set('Europe/Paris');
	}

	/**
	 * @throws Exception
	 */
	public function test_now()
	{
		$d = DateTime::now();
		$now = new \DateTime('@' . $_SERVER['REQUEST_TIME']);
		$now->setTimezone(new DateTimeZone(date_default_timezone_get()));
		$this->assertEquals(date_default_timezone_get(), $d->zone->name);
		$this->assertEquals($d->year, $now->format('Y'));
		$this->assertEquals($d->month, $now->format('m'));
		$this->assertEquals($d->day, $now->format('d'));
		$this->assertEquals($d->hour, $now->format('H'));
		$this->assertEquals($d->minute, $now->format('i'));
		$this->assertEquals($d->second, $now->format('s'));

		sleep(2);

		$this->assertEquals($d, DateTime::now());
		$this->assertGreaterThan($d, DateTime::right_now());
	}

	public function test_none()
	{
		$d = DateTime::none();
		$this->assertEquals('UTC', $d->zone->name);
		$this->assertEquals(-1, $d->year);
		$this->assertEquals(11, $d->month);
		$this->assertEquals(30, $d->day);
		$this->assertSame("", (string) $d);

		$d = DateTime::none('Asia/Tokyo');
		$this->assertEquals('Asia/Tokyo', $d->zone->name);
		$this->assertTrue($d->is_empty);
		$this->assertSame("", (string) $d);

		$d = DateTime::none(new DateTimeZone('Asia/Tokyo'));
		$this->assertEquals('Asia/Tokyo', $d->zone->name);
		$this->assertTrue($d->is_empty);
	}

	/**
	 * @throws Exception
	 */
	public function test_from()
	{
		$d = DateTime::from(new \DateTime('2001-01-01 01:01:01', new DateTimeZone('Europe/Paris')));
		$this->assertEquals('Europe/Paris', $d->zone->name);
		$this->assertEquals('2001-01-01 01:01:01', $d->as_db);

		$d = DateTime::from(new \DateTime('2001-01-01 01:01:01', new DateTimeZone('Europe/Paris')), new DateTimeZone('UTC'));
		$this->assertEquals('Europe/Paris', $d->zone->name);
		$this->assertEquals('2001-01-01 01:01:01', $d->as_db);

		$d = DateTime::from(new \DateTime('2001-01-01 01:01:01', new DateTimeZone('UTC')));
		$this->assertEquals('UTC', $d->zone->name);
		$this->assertEquals('2001-01-01 01:01:01', $d->as_db);

		$d = DateTime::from(new \DateTime('2001-01-01 01:01:01', new DateTimeZone('UTC')), new DateTimeZone('Europe/Paris'));
		$this->assertEquals('UTC', $d->zone->name);
		$this->assertEquals('2001-01-01 01:01:01', $d->as_db);

		$d = DateTime::from('2001-01-01 01:01:01', new DateTimeZone('UTC'));
		$this->assertEquals('UTC', (string) $d->zone);
		$this->assertEquals('2001-01-01 01:01:01', $d->as_db);

		$d = DateTime::from('2001-01-01 01:01:01', new DateTimeZone('Europe/Paris'));
		$this->assertEquals('Europe/Paris', $d->zone->name);
		$this->assertEquals('2001-01-01 01:01:01', $d->as_db);

		$d = DateTime::from('2001-01-01 01:01:01');
		$this->assertEquals(date_default_timezone_get(), $d->zone->name);
		$this->assertEquals('2001-01-01 01:01:01', $d->as_db);
	}

	public function test_from_instance()
	{
		$d = new \DateTime;
		$this->assertInstanceOf(DateTime::class, DateTime::from($d));
		$this->assertInstanceOf(MyDateTime::class, MyDateTime::from($d));

		$d = new DateTime;
		$this->assertInstanceOf(DateTime::class, DateTime::from($d));
		$this->assertInstanceOf(MyDateTime::class, MyDateTime::from($d));
	}

	public function test_change()
	{
		$d = new DateTime('2001-01-01 01:01:01');

		$this->assertEquals('2009-01-01 01:01:01', $d->change([ 'year' => 2009 ])->as_db);
		$this->assertEquals(2009, $d->year);
		$this->assertEquals('2009-09-01 01:01:01', $d->change([ 'month' => 9 ])->as_db);
		$this->assertEquals(9, $d->month);
		$this->assertEquals('2009-09-09 01:01:01', $d->change([ 'day' => 9 ])->as_db);
		$this->assertEquals(9, $d->day);
		$this->assertEquals('2009-09-09 09:01:01', $d->change([ 'hour' => 9 ])->as_db);
		$this->assertEquals(9, $d->hour);
		$this->assertEquals('2009-09-09 09:09:01', $d->change([ 'minute' => 9 ])->as_db);
		$this->assertEquals(9, $d->minute);
		$this->assertEquals('2009-09-09 09:09:09', $d->change([ 'second' => 9 ])->as_db);
		$this->assertEquals(9, $d->second);

		$d->change([

			'year' => 2001,
			'month' => 1,
			'day' => 1,
			'hour' => 1,
			'minute' => 1,
			'second' => 1,

		]);

		$this->assertEquals('2001-01-01 01:01:01', $d->as_db);
	}

	public function provider_test_change_cascade(): array
	{
		return [

			[ '2001-01-01 01:02:00', [ 'minute' => 2 ] ],
			[ '2001-01-01 02:00:00', [ 'hour' => 2 ] ],
			[ '2001-01-01 01:02:02', [ 'minute' => 2, 'second' => 2 ] ],
			[ '2001-01-01 02:02:00', [ 'hour' => 2, 'minute' => 2 ] ],
			[ '2001-01-01 02:02:02', [ 'hour' => 2, 'minute' => 2, 'second' => 2 ] ],
			[ '2001-01-01 01:00:00', [ 'minute' => 0 ] ],
			[ '2001-01-01 00:00:00', [ 'hour' => 0 ] ],

		];
	}

	public function test_change_on_timezone_option(): void
	{
		$expectedTimezone = 'Asia/Taipei';
		$now = new DateTime();
		$options = [

			'timezone' => $expectedTimezone,

		];
		$result = $now->change($options);
		$resultTimezone = $result->getTimezone();

		$this->assertSame($expectedTimezone, $resultTimezone->getName());
	}

	/**
	 * @dataProvider provider_test_change_cascade
	 */
	public function test_change_cascade($expected_datetime, $change_format): void
	{
		$datetime = '2001-01-01 01:01:01';

		$d = new DateTime($datetime);
		$this->assertEquals($expected_datetime, $d->change($change_format, true)->as_db);
	}

	public function test_with(): void
	{
		$d = new DateTime('2001-01-01 01:01:01');
		$e = $d->with([ 'year' => 2015, 'month' => 5, 'day' => 5 ]);

		$this->assertNotSame($d, $e);
		$this->assertEquals(2001, $d->year);
		$this->assertEquals(2015, $e->year);
		$this->assertEquals('2001-01-01 01:01:01', $d->as_db);
		$this->assertEquals('2015-05-05 01:01:01', $e->as_db);
	}

	public function provider_test_get_year(): array
	{
		return [

			[ '2012-12-16 15:00:00', 2012 ],
			[ '0000-12-16 15:00:00', 0 ],
			[ '9999-12-16 15:00:00', 9999 ],

		];
	}

	/**
	 * @dataProvider provider_test_get_year
	 */
	public function test_get_year($datetime, $expected): void
	{
		$d = new DateTime($datetime);
		$this->assertEquals($expected, $d->year);
	}

	public function test_set_year(): void
	{
		$d = new DateTime('2001-01-01 01:01:01');
		$d->year = 2009;
		$this->assertEquals('2009-01-01 01:01:01', $d->as_db);
	}

	public function provider_test_get_quarter(): array
	{
		return [

			[ '2012-01-16 15:00:00', 1 ],
			[ '2012-02-16 15:00:00', 1 ],
			[ '2012-03-16 15:00:00', 1 ],
			[ '2012-04-16 15:00:00', 2 ],
			[ '2012-05-16 15:00:00', 2 ],
			[ '2012-06-16 15:00:00', 2 ],
			[ '2012-07-16 15:00:00', 3 ],
			[ '2012-08-16 15:00:00', 3 ],
			[ '2012-09-16 15:00:00', 3 ],
			[ '2012-10-16 15:00:00', 4 ],
			[ '2012-11-16 15:00:00', 4 ],
			[ '2012-12-16 15:00:00', 4 ],

		];
	}

	/**
	 * @dataProvider provider_test_get_quarter
	 */
	public function test_get_quarter($datetime, $expected)
	{
		$d = new DateTime($datetime);
		$this->assertEquals($expected, $d->quarter);
	}

	public function provider_test_get_month(): array
	{
		return [

			[ '2012-01-16 15:00:00', 1 ],
			[ '2012-06-16 15:00:00', 6 ],
			[ '2012-12-16 15:00:00', 12 ],

		];
	}

	/**
	 * @dataProvider provider_test_get_month
	 */
	public function test_get_month($datetime, $expected)
	{
		$d = new DateTime($datetime);
		$this->assertEquals($expected, $d->month);
	}

	public function test_set_month(): void
	{
		$d = new DateTime('2001-01-01 01:01:01');
		$d->month = 9;
		$this->assertEquals('2001-09-01 01:01:01', $d->as_db);
	}

	/**
	 * @dataProvider provide_test_get_week
	 */
	public function test_get_week($datetime, $expected): void
	{
		$d = new DateTime($datetime);
		$this->assertEquals($expected, $d->week);
	}

	public function provide_test_get_week(): array
	{
		return [

			[ '2012-01-01 15:00:00', 52 ],
			[ '2012-01-16 15:00:00', 3 ],

		];
	}

	/**
	 * @dataProvider provide_test_get_year_day
	 */
	public function test_get_year_day($datetime, $expected)
	{
		$d = new DateTime($datetime);
		$this->assertEquals($expected, $d->year_day);
	}

	public function provide_test_get_year_day(): array
	{
		return [

			[ '2012-01-01 15:00:00', 1 ],
			[ '2012-12-31 15:00:00', 366 ],

		];
	}

	/**
	 * Sunday must be 7, Monday must be 1.
	 *
	 * @dataProvider provide_test_get_weekday
	 */
	public function test_get_weekday($datetime, $expected)
	{
		$d = new DateTime($datetime);
		$this->assertEquals($expected, $d->weekday);
	}

	public function provide_test_get_weekday(): array
	{
		return [

			[ '2012-12-17 15:00:00', 1 ],
			[ '2012-12-18 15:00:00', 2 ],
			[ '2012-12-19 15:00:00', 3 ],
			[ '2012-12-20 15:00:00', 4 ],
			[ '2012-12-21 15:00:00', 5 ],
			[ '2012-12-22 15:00:00', 6 ],
			[ '2012-12-23 15:00:00', 7 ],

		];
	}

	/**
	 * @dataProvider provide_test_get_day
	 */
	public function test_get_day($datetime, $expected)
	{
		$d = new DateTime($datetime);
		$this->assertEquals($expected, $d->day);
	}

	public function provide_test_get_day(): array
	{
		return [

			[ '2012-12-16 15:00:00', 16 ],
			[ '2012-12-17 15:00:00', 17 ],
			[ '2013-01-01 03:00:00', 1 ],

		];
	}

	public function test_set_day()
	{
		$d = new DateTime('2001-01-01 01:01:01');
		$d->day = 9;
		$this->assertEquals('2001-01-09 01:01:01', $d->as_db);
	}

	public function test_get_hour()
	{
		$d = new DateTime('2013-01-01 01:23:45');
		$this->assertEquals(1, $d->hour);
	}

	public function test_set_hour()
	{
		$d = new DateTime('2001-01-01 01:01:01');
		$d->hour = 9;
		$this->assertEquals('2001-01-01 09:01:01', $d->as_db);
	}

	public function test_get_minute()
	{
		$d = new DateTime('2013-01-01 01:23:45');
		$this->assertEquals(23, $d->minute);
	}

	public function test_set_minute()
	{
		$d = new DateTime('2001-01-01 01:01:01');
		$d->minute = 9;
		$this->assertEquals('2001-01-01 01:09:01', $d->as_db);
	}

	public function test_get_second()
	{
		$d = new DateTime('2013-01-01 01:23:45');
		$this->assertEquals(45, $d->second);
	}

	public function test_set_second()
	{
		$d = new DateTime('2001-01-01 01:01:01');
		$d->second = 9;
		$this->assertEquals('2001-01-01 01:01:09', $d->as_db);
	}

	public function test_get_is_monday()
	{
		$d = new DateTime('2013-02-04 21:00:00', 'utc');
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
		$d = new DateTime('2013-02-05 21:00:00', 'utc');
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
		$d = new DateTime('2013-02-06 21:00:00', 'utc');
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
		$d = new DateTime('2013-02-07 21:00:00', 'utc');
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
		$d = new DateTime('2013-02-08 21:00:00', 'utc');
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
		$d = new DateTime('2013-02-09 21:00:00', 'utc');
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
		$d = new DateTime('2013-02-10 21:00:00', 'utc');
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
		$d = DateTime::now();
		$d->zone = 'Asia/Tokyo';
		$this->assertTrue($d->is_today);
		$this->assertFalse($d->tomorrow->is_today);
		$this->assertFalse($d->yesterday->is_today);
	}

	public function test_get_is_past()
	{
		$d = DateTime::now();
		$d->zone = 'Asia/Tokyo';
		$d->timestamp -= 3600;
		$this->assertTrue($d->is_past);
		$d->timestamp += 7200;
		$this->assertFalse($d->is_past);
	}

	public function test_get_is_future()
	{
		$d = DateTime::now();
		$d->zone = 'Asia/Tokyo';
		$d->timestamp -= 3600;
		$this->assertFalse($d->is_future);
		$d->timestamp += 7200;
		$this->assertTrue($d->is_future);
	}

	public function test_get_is_empty()
	{
		$d = DateTime::none();
		$this->assertTrue($d->is_empty);
		$d = new DateTime('0000-00-00 00:00:00');
		$this->assertTrue($d->is_empty);
		$d = new DateTime('0000-00-00');
		$this->assertTrue($d->is_empty);
		$d = new DateTime('now');
		$this->assertFalse($d->is_empty);
		$d = new DateTime('@0');
		$this->assertFalse($d->is_empty);
	}

	public function test_get_tomorrow()
	{
		$d = new DateTime('2013-02-10 21:21:21', 'utc');
		$this->assertEquals('2013-02-11 00:00:00', $d->tomorrow->as_db);
	}

	public function test_get_yesterday()
	{
		$d = new DateTime('2013-02-10 21:21:21', 'utc');
		$this->assertEquals('2013-02-09 00:00:00', $d->yesterday->as_db);
	}

	/**
	 * @dataProvider provide_test_day_instance
	 */
	public function test_day_instance($date, $expected, $day)
	{
		$d = new DateTime($date);
		$this->assertEquals($expected, $d->$day->as_db);
	}

	public function provide_test_day_instance(): array
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
		$d = new DateTime('-4712-12-07 12:06:46');
		$this->assertEquals(-4712, $d->year);
		$this->assertEquals('-4712-12-07 12:06:46', $d->as_db);
	}

	public function test_far_future()
	{
		$d = new DateTime('4712-12-07 12:06:46');
		$this->assertEquals(4712, $d->year);
		$this->assertEquals('4712-12-07 12:06:46', $d->as_db);
	}

	public function test_get_utc()
	{
		$d = new DateTime('2013-03-06 18:00:00', 'Europe/Paris');
		$utc = $d->utc;

		$this->assertEquals('UTC', $utc->zone->name);
		$this->assertEquals('2013-03-06 17:00:00', $utc->as_db);
	}

	public function test_get_is_utc()
	{
		$d = DateTime::now();
		$d->zone = 'Asia/Tokyo';
		$this->assertFalse($d->is_utc);
		$d->zone = 'UTC';
		$this->assertTrue($d->is_utc);
	}

	public function test_get_local()
	{
		$d = new DateTime('2013-03-06 17:00:00', 'UTC');
		$local = $d->local;

		$this->assertEquals('Europe/Paris', $local->zone->name);
		$this->assertEquals('2013-03-06 18:00:00', $local->as_db);
	}

	public function test_get_is_local()
	{
		$d = DateTime::now();
		$d->zone = date_default_timezone_get() == 'UTC' ? 'Asia/Tokyo' : 'UTC';
		$this->assertFalse($d->is_local);
		$d->zone = date_default_timezone_get();
		$this->assertTrue($d->is_local);
	}

	public function test_get_is_dst()
	{
		$d = new DateTime('2013-02-03 21:00:00');
		$this->assertFalse($d->is_dst);
		$d = new DateTime('2013-08-03 21:00:00');
		$this->assertTrue($d->is_dst);
	}

	public function test_format()
	{
		$empty = DateTime::none();
		$this->assertEquals('0000-00-00', $empty->format(DateTime::DATE));
		$this->assertEquals('0000-00-00 00:00:00', $empty->format(DateTime::DB));
		$this->assertStringEndsWith('30 Nov -0001 00:00:00 +0000', $empty->format(DateTimeInterface::RSS));
	}

	/*
	 * Predefined formats
	 */

	public function test_format_as_atom()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTimeInterface::ATOM), $now->format_as_atom());
	}

	public function test_get_as_atom()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTimeInterface::ATOM), $now->as_atom);
	}

	public function test_format_as_cookie()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTimeInterface::COOKIE), $now->format_as_cookie());
	}

	public function test_get_as_cookie()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTimeInterface::COOKIE), $now->as_cookie);

		$date = new DateTime('2013-11-04 20:21:22 UTC');
		$this->assertEquals("Monday, 04-Nov-2013 20:21:22 UTC", $date->as_cookie);
	}

	public function test_format_as_iso8601()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTimeInterface::ISO8601), $now->format_as_iso8601());
	}

	public function test_format_as_iso8601_utc()
	{
		$now = DateTime::now()->utc;
		$this->assertEquals(str_replace('+0000', 'Z', $now->format(DateTimeInterface::ISO8601)), $now->format_as_iso8601());
	}

	public function test_get_as_iso8601()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTimeInterface::ISO8601), $now->as_iso8601);
	}

	public function test_as_iso8601_utc()
	{
		$now = DateTime::now()->utc;
		$this->assertEquals(str_replace('+0000', 'Z', $now->format(DateTimeInterface::ISO8601)), $now->as_iso8601);
	}

	public function test_format_as_rfc822()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTimeInterface::RFC822), $now->format_as_rfc822());
	}

	public function test_format_as_rfc822_utc()
	{
		$now = DateTime::now()->utc;
		$this->assertEquals(str_replace('+0000', 'GMT', $now->format(DateTimeInterface::RFC822)), $now->format_as_rfc822());
	}

	public function test_get_as_rfc822()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTimeInterface::RFC822), $now->as_rfc822);
	}

	public function test_get_as_rfc822_utc()
	{
		$now = DateTime::now()->utc;
		$this->assertEquals(str_replace('+0000', 'GMT', $now->format(DateTimeInterface::RFC822)), $now->as_rfc822);
	}

	public function test_format_as_rfc850()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTimeInterface::RFC850), $now->format_as_rfc850());
	}

	public function test_get_as_rfc850()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTimeInterface::RFC850), $now->as_rfc850);
	}

	public function test_format_as_rfc1036()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTimeInterface::RFC1036), $now->format_as_rfc1036());
	}

	public function test_get_as_rfc1036()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTimeInterface::RFC1036), $now->as_rfc1036);
	}

	public function test_format_as_rfc1123()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTimeInterface::RFC1123), $now->format_as_rfc1123());
	}

	public function test_format_as_rfc1123_utc()
	{
		$now = DateTime::now()->utc;
		$this->assertEquals(str_replace('+0000', 'GMT', $now->format(DateTimeInterface::RFC1123)), $now->format_as_rfc1123());
	}

	public function test_get_as_rfc1123()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTimeInterface::RFC1123), $now->as_rfc1123);
	}

	public function test_get_as_rfc1123_utc()
	{
		$now = DateTime::now()->utc;
		$this->assertEquals(str_replace('+0000', 'GMT', $now->format(DateTimeInterface::RFC1123)), $now->as_rfc1123);
	}

	public function test_format_as_rfc2822()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTimeInterface::RFC2822), $now->format_as_rfc2822());
	}

	public function test_get_as_rfc2822()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTimeInterface::RFC2822), $now->as_rfc2822);
	}

	public function test_format_as_rfc3339()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTimeInterface::RFC3339), $now->format_as_rfc3339());
	}

	public function test_get_as_rfc3339()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTimeInterface::RFC3339), $now->as_rfc3339);
	}

	public function test_format_as_rss()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTimeInterface::RSS), $now->format_as_rss());
	}

	public function test_get_as_rss()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTimeInterface::RSS), $now->as_rss);
	}

	public function test_format_as_w3c()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTimeInterface::W3C), $now->format_as_w3c());
	}

	public function test_get_as_w3c()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTimeInterface::W3C), $now->as_w3c);
	}

	public function test_format_as_db()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTime::DB), $now->format_as_db());
		$empty = DateTime::none();
		$this->assertEquals('0000-00-00 00:00:00', $empty->format_as_db());
	}

	public function test_get_as_db()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTime::DB), $now->as_db);
		$empty = DateTime::none();
		$this->assertEquals('0000-00-00 00:00:00', $empty->as_db);
	}

	public function test_format_as_number()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTime::NUMBER), $now->format_as_number());
	}

	public function test_get_as_number()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTime::NUMBER), $now->as_number);
	}

	public function test_format_as_date()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTime::DATE), $now->format_as_date());
		$empty = DateTime::none();
		$this->assertEquals('0000-00-00', $empty->format_as_date());
	}

	public function test_get_as_date()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTime::DATE), $now->as_date);
		$empty = DateTime::none();
		$this->assertEquals('0000-00-00', $empty->as_date);
	}

	public function test_format_as_time()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTime::TIME), $now->format_as_time());
	}

	public function test_get_as_time()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTime::TIME), $now->as_time);
	}

	public function test_compare()
	{
		$d1 = DateTime::now();
		$d2 = DateTime::now();

		$this->assertSame((string) $d1, (string) $d2);

		$d2->second++;
		$this->assertNotSame((string) $d1, (string) $d2);
		$this->assertLessThan($d2, $d1);
		$this->assertGreaterThan($d1, $d2);

		$now = DateTime::now();
		$yesterday = $now->yesterday;
		$tomorrow = $now->tomorrow;

		$this->assertGreaterThan($yesterday, $now);
		$this->assertLessThan($tomorrow, $now);
		$this->assertSame($yesterday, min($now, $yesterday, $tomorrow));
		$this->assertSame($tomorrow, max($now, $yesterday, $tomorrow));
	}

	public function test_json_serialize()
	{
		$date = new DateTime("2014-10-23 13:50:10", "Europe/Paris");
		$this->assertEquals('{"date":"2014-10-23T13:50:10+0200"}', json_encode([ 'date' => $date ]));
	}

	public function test_localize()
	{
		$invoked = false;
		$reference = DateTime::now();

		DateTime::$localizer = function (DateTime $datetime, $locale) use (&$invoked, &$reference) {

			$this->assertSame($datetime, $reference);
			$this->assertEquals('fr', $locale);

		};

		$reference->localize('fr');
	}

	/**
	 * @dataProvider provide_read_only_property
	 */
	public function test_read_only_property(string $property): void
	{
		$d = DateTime::now();
		$this->expectException(PropertyNotWritable::class);
		$this->expectExceptionMessageMatches("/The property `$property` .+ is not writable./");
		$d->{$property} = null;
	}

	public function provide_read_only_property(): array
	{
		$read_only_properties = <<<EOT
		as_time 
		as_date
		as_number
		as_db
		as_w3c
		as_rfc822 
		as_rfc3339
		as_rss 
		as_rfc2822 
		as_iso8601
		as_rfc850
		as_rfc1036
		as_rfc1123
		as_cookie
		as_atom
		utc
		local
		monday
		tuesday
		wednesday
		thursday
		friday
		saturday
		sunday
		yesterday
		tomorrow
		is_dst
		is_utc
		is_local
		is_monday
		is_tuesday
		is_wednesday
		is_thursday
		is_friday
		is_saturday
		is_sunday
		is_today
		is_past
		is_future
		is_empty
		year_day
		week
		weekday
		quarter
		EOT;

		return array_map(
			fn($property): array => [ trim($property) ],
			preg_split('/\s+/', $read_only_properties)
		);
	}

	public function test_getting_undefined_property_should_throw_exception(): void
	{
		$date = DateTime::now();
		$property = uniqid();
		$this->expectExceptionMessageMatches("/Undefined property/");
		$date->$property;
	}
}

namespace Tests\ICanBoogie;

use ICanBoogie\DateTime;

class MyDateTime extends DateTime
{

}
