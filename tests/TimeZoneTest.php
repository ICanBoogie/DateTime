<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\ICanBoogie;

use ICanBoogie\PropertyNotDefined;
use ICanBoogie\TimeZone;
use ICanBoogie\TimeZoneLocation;
use PHPUnit\Framework\TestCase;

final class TimeZoneTest extends TestCase
{
	public function test_get_location(): void
	{
		$z = new TimeZone('Europe/Paris');
		$this->assertInstanceOf(TimeZoneLocation::class, $z->location);
	}

	public function test_get_name(): void
	{
		$z = new TimeZone('Europe/Paris');
		$this->assertEquals($z->getName(), $z->name);
	}

	public function test_get_offset(): void
	{
		$z = new TimeZone('Europe/Paris');
		$utc = new \DateTime('now', new \DateTimeZone('utc'));

		$this->assertEquals($z->getOffset($utc), $z->offset);
	}

	public function test_utc_case(): void
	{
		$this->assertEquals('UTC', TimeZone::from('utc')->name);
		$this->assertEquals('UTC', TimeZone::from('UTC')->name);
		$this->assertEquals('UTC', (new TimeZone('utc'))->name);
		$this->assertEquals('UTC', (new TimeZone('UTC'))->name);
	}

	public function test_should_create_from_php_datetimezone(): void
	{
		$name = 'Europe/Paris';
		$z1 = new \DateTimeZone($name);
		$z2 = TimeZone::from($z1);

		$this->assertSame($name, $z2->name);
	}

	public function test_should_reuse_instance(): void
	{
		$z1 = TimeZone::from('utc');
		$z2 = TimeZone::from('utc');

		$this->assertSame($z1, $z2);
	}

	public function test_should_reuse_timezone(): void
	{
		$z1 = TimeZone::from('Europe/Paris');
		$z2 = TimeZone::from($z1);

		$this->assertSame($z1, $z2);
	}

	public function test_getting_undefined_property_should_throw_exception(): void
	{
		$property = uniqid();
		$z1 = TimeZone::from('utc');
		$this->expectException(PropertyNotDefined::class);
		$z1->$property;
	}

	public function test_to_string(): void
	{
		$name = 'Europe/Paris';
		$z1 = TimeZone::from($name);

		$this->assertSame($name, (string) $z1);
	}
}
