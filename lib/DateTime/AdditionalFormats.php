<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie\DateTime;

/**
 *
 */
interface AdditionalFormats
{
	/**
	 * DB (example: 2013-02-03 20:59:03)
	 *
	 * @var string
	 */
	const DB = 'Y-m-d H:i:s';

	/**
	 * Number (example: 20130203205903)
	 *
	 * @var string
	 */
	const NUMBER = 'YmdHis';

	/**
	 * Date (example: 2013-02-03)
	 *
	 * @var string
	 */
	const DATE = 'Y-m-d';

	/**
	 * Time (example: 20:59:03)
	 *
	 * @var string
	 */
	const TIME = 'H:i:s';
}
