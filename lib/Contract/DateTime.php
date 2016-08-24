<?php

namespace ICanBoogie\Contract;

/**
 * Interface for ICanBoogie's mutable and immutable DateTime.
 */
interface DateTime extends \DateTimeInterface
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

	/**
	 * Creates a {@link DateTime} instance from a source.
	 *
	 * <pre>
	 * <?php
	 *
	 * use ICanBoogie\DateTime;
	 *
	 * DateTime::from(new \DateTime('2001-01-01 01:01:01', new \DateTimeZone('Europe/Paris')));
	 * DateTime::from('2001-01-01 01:01:01', 'Europe/Paris');
	 * DateTime::from('now');
	 * </pre>
	 *
	 * @param mixed $source
	 * @param mixed $timezone The time zone to use to create the time. The value is ignored if the
	 * source is an instance of {@link \DateTime}.
	 *
	 * @return static
	 */
	static public function from($source, $timezone = null);

	/**
	 * Returns an instance representing an empty date ("0000-00-00").
	 *
	 * <pre>
	 * <?php
	 *
	 * use ICanBoogie\DateTime;
	 *
	 * $d = DateTime::none();
	 * $d->is_empty;                      // true
	 * $d->timezone->name;                // "UTC"
	 *
	 * $d = DateTime::none('Asia/Tokyo');
	 * $d->is_empty;                      // true
	 * $d->timezone->name;                // "Asia/Tokio"
	 * </pre>
	 *
	 * @param \DateTimeZone|string $timezone The time zone in which the empty date is created.
	 * Defaults to "UTC".
	 *
	 * @return DateTime
	 */
	static public function none($timezone = 'utc');

	/**
	 * Returns an instance with the current local time and the local time zone.
	 *
	 * **Note:** Subsequent calls return equal times, event if they are minutes apart. _now_
	 * actually refers to the `REQUEST_TIME` or, if it is now available, to the first time
	 * the method was invoked.
	 *
	 * @return static
	 */
	static public function now();

	/**
	 * Returns an instance with the current local time and the local time zone.
	 *
	 * **Note:** Subsequent calls may return different times.
	 *
	 * @return static
	 */
	static public function right_now();

	/**
	 * Modifies the properties of the instance according to the options.
	 *
	 * The following properties can be updated: {@link $year}, {@link $month}, {@link $day},
	 * {@link $hour}, {@link $minute} and {@link $second}.
	 *
	 * Note: Values exceeding ranges are added to their parent values.
	 *
	 * <pre>
	 * <?php
	 *
	 * use ICanBoogie\DateTime;
	 *
	 * $time = new DateTime('now');
	 * $time->change([ 'year' => 2000, 'second' => 0 ]);
	 * </pre>
	 *
	 * @param array $options
	 * @param bool $cascade If `true`, time options (`hour`, `minute`, `second`) reset
	 * cascading, so if only the hour is passed, then minute and second is set to 0. If the hour
	 * and minute is passed, then second is set to 0.
	 *
	 * @return $this
	 */
	public function change(array $options, $cascade = false);

	/**
	 * Returns a new instance with changes properties.
	 *
	 * @param array $options
	 * @param bool $cascade
	 *
	 * @return DateTime
	 */
	public function with(array $options, $cascade = false);

	/**
	 * Returns a localized instance.
	 *
	 * @param string $locale
	 *
	 * @return mixed
	 *
	 * @throws \LogicException if the instance cannot be localized.
	 */
	public function localize($locale = 'en');
}
