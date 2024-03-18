<?php

namespace ICanBoogie\DateTime;

/**
 * A date without a time-zone in the ISO-8601 calendar system, such as 2007-12-03.
 */
readonly class LocalDate
{
    use LocalDateTrait;
    use IntervalTrait;

    /**
     * The {@see \DateTimeImmutable} representation of this value.
     */
    public \DateTimeImmutable $delegate;

    /**
     * The default format for this value.
     */
    public const DEFAULT_FORMAT = 'Y-m-d';

    public static function from(string|\DateTimeInterface $value): self
    {
        $delegate = $value instanceof \DateTimeInterface
            ? $value
            : new \DateTimeImmutable($value);

        return new self(
            (int) $delegate->format('Y'),
            (int) $delegate->format('n'),
            (int) $delegate->format('j'),
        );
    }

    /**
     * @param int $year
     *     The year component of the date.
     * @param int|Month $monthOrNumber
     *     The month or number-of-month (1..12) component of the date.
     * @param int $dayOfMonth
     *     The day component of the date.
     */
    public function __construct(
        int $year,
        int|Month $monthOrNumber,
        int $dayOfMonth,
    ) {
        $this->delegate = $this->constructDate($year, $monthOrNumber, $dayOfMonth);
    }

    public function __toString(): string
    {
        return $this->format(self::DEFAULT_FORMAT);
    }

    /**
     * Formats this value using the given format.
     *
     * @see \DateTimeInterface::format()
     */
    public function format(string $format): string
    {
        return $this->delegate->format($format);
    }

    public function at(
        int $hour,
        int $minute,
        int $second = 0,
        int $microsecond = 0,
    ): LocalDateTime {
        $at = $this->delegate
            ->setTime($hour, $minute, $second, $microsecond);

        return LocalDateTime::from($at);
    }

    public function atTime(LocalTime $time): LocalDateTime
    {
        return $this->at($time->hour, $time->minute, $time->second, $time->microsecond);
    }
}
