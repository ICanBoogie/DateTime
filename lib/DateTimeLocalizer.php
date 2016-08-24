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
 * Localizes DateTime instances.
 */
class DateTimeLocalizer
{
	const DEFAULT_LOCALE = 'en';

	/**
	 * DateTime localizer.
	 *
	 * @var callable
	 */
	static private $localizer;

	/**
	 * Defines the localizer.
	 *
	 * @param callable $localizer
	 *
	 * @return callable The previous localizer, or `null` if none was defined.
	 */
	static public function define(callable $localizer)
	{
		$previous = self::$localizer;

		self::$localizer = $localizer;

		return $previous;
	}

	/**
	 * Returns the current localizer.
	 *
	 * @return callable|null
	 */
	static public function defined()
	{
		return self::$localizer;
	}

	/**
	 * Undefine the localizer.
	 */
	static public function undefine()
	{
		self::$localizer = null;
	}

	/**
	 * Localize a {@link DateTime} instance.
	 *
	 * @param DateTime $datetime
	 * @param string $locale
	 *
	 * @return callable
	 */
	static public function localize(DateTime $datetime, $locale = self::DEFAULT_LOCALE)
	{
		$provider = self::$localizer;

		if (!$provider)
		{
			throw new \LogicException("No localizer is defined yet. Please define one with `DateTimeLocalizer::define(\$localizer)`.");
		}

		return $provider($datetime, $locale);
	}
}
