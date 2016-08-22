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

class DateTimeTest extends AbstractDateTimeTest
{
	/**
	 * @inheritdoc
	 *
	 * @return DateTime
	 */
	protected function now()
	{
		return DateTime::now();
	}

	/**
	 * @inheritdoc
	 *
	 * @return DateTime
	 */
	protected function right_now()
	{
		return DateTime::right_now();
	}

	/**
	 * @inheritdoc
	 *
	 * @return DateTime
	 */
	protected function none($timezone = 'utc')
	{
		return DateTime::none($timezone);
	}

	/**
	 * @inheritdoc
	 *
	 * @return DateTime
	 */
	protected function from($source, $timezone = null)
	{
		return DateTime::from($source, $timezone);
	}

	/**
	 * @inheritdoc
	 *
	 * @return DateTime
	 */
	protected function create($time = 'now', $timezone = null)
	{
		return new DateTime($time, $timezone);
	}

	/*
	 * Different expectations
	 */

	public function test_from_instance()
	{
		$d = new \DateTime;
		$this->assertInstanceOf(DateTime::class, $this->from($d));
		$this->assertInstanceOf(ExtendedDateTime::class, ExtendedDateTime::from($d));

		$d = $this->create();
		$this->assertInstanceOf(DateTime::class, $this->from($d));
		$this->assertInstanceOf(ExtendedDateTime::class, ExtendedDateTime::from($d));
	}

	public function test_now_should_provide_the_same_instance()
	{
		$now = $this->now();

		$this->assertSame($now, DateTime::now());
	}

	public function test_get_is_past()
	{
		$d = $this->now()->setTimezone('Asia/Tokyo');
		$this->assertTrue($d->modify('-3600 second')->is_past);
		$this->assertFalse($d->modify('+3500 second')->is_past);
	}

	public function test_get_is_future()
	{
		$d = $this->now()->setTimezone('Asia/Tokyo');
		$this->assertFalse($d->modify('-3600 second')->is_future);
		$this->assertTrue($d->modify('+3600 second')->is_future);
	}

	/**
	 * @dataProvider provide_test_should_be_same_class
	 *
	 * @param string $property
	 */
	public function test_should_be_same_class($property)
	{
		$this->assertInstanceOf(DateTime::class, $this->now()->$property);
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
