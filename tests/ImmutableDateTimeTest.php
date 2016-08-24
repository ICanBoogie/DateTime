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
 */
class ImmutableDateTimeTest extends AbstractDateTimeTest
{
	/**
	 * @inheritdoc
	 *
	 * @return ImmutableDateTime
	 */
	protected function now()
	{
		return ImmutableDateTime::now();
	}

	/**
	 * @inheritdoc
	 *
	 * @return ImmutableDateTime
	 */
	protected function right_now()
	{
		return ImmutableDateTime::right_now();
	}

	/**
	 * @inheritdoc
	 *
	 * @return ImmutableDateTime
	 */
	protected function none($timezone = 'utc')
	{
		return ImmutableDateTime::none($timezone);
	}

	/**
	 * @inheritdoc
	 *
	 * @return ImmutableDateTime
	 */
	protected function from($source, $timezone = null)
	{
		return ImmutableDateTime::from($source, $timezone);
	}

	/**
	 * @inheritdoc
	 *
	 * @return ImmutableDateTime
	 */
	protected function create($time = 'now', $timezone = null)
	{
		return new ImmutableDateTime($time, $timezone);
	}

	/*
	 * Different expectations
	 */

	public function test_from_instance()
	{
		$d = new \DateTime;
		$this->assertInstanceOf(ImmutableDateTime::class, $this->from($d));
		$this->assertInstanceOf(ExtendedImmutableDateTime::class, ExtendedImmutableDateTime::from($d));

		$d = $this->create();
		$this->assertInstanceOf(ImmutableDateTime::class, $this->from($d));
		$this->assertInstanceOf(ExtendedImmutableDateTime::class, ExtendedImmutableDateTime::from($d));
	}

	public function test_now_should_provide_the_same_instance()
	{
		$now = $this->now();

		$this->assertSame($now, ImmutableDateTime::now());
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
		$this->assertInstanceOf(ImmutableDateTime::class, $this->now()->$property);
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
