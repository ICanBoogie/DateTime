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

use ICanBoogie\TimeZone;

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

	public function test_reuse()
	{
		$z1 = TimeZone::from('utc');
		$z2 = TimeZone::from('utc');

		$this->assertEquals(spl_object_hash($z1), spl_object_hash($z2));
	}
}