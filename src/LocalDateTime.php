<?php

namespace ICanBoogie\DateTime;

/**
 * Represents a specific civil date and time without a reference to a particular time zone.
 */
readonly class LocalDateTime
{
    use LocalDateTrait;
    use LocalTimeTrait;
    use IntervalTrait;

    /**
     * The default format for this value.
     */
    public const DEFAULT_FORMAT = "Y-m-d\TH:i:s\.u";

    public static function from(string|\DateTimeInterface $value): self
    {
        $delegate = $value instanceof \DateTimeInterface
            ? $value
            : new \DateTimeImmutable($value);

        return new self(
            (int) $delegate->format('Y'),
            (int) $delegate->format('n'),
            (int) $delegate->format('j'),
            (int) $delegate->format('G'),
            (int) ltrim($delegate->format('i'), '0'),
            (int) ltrim($delegate->format('s'), '0'),
            (int) ltrim($delegate->format('u'), '0'),
        );
    }

    /**
     * The {@see \DateTimeImmutable} representation of this value.
     */
    public \DateTimeImmutable $delegate;

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
        int $hour,
        int $minute,
        int $second = 0,
        int $microsecond = 0,
    ) {
        $this->constructDate($year, $monthOrNumber, $dayOfMonth);
        $this->constructTime($hour, $minute, $second, $microsecond);

        $this->delegate = (new \DateTimeImmutable(timezone: TimeZone::utc()->delegate))
            ->setDate($year, $this->monthNumber, $this->dayOfMonth)
            ->setTime($hour, $minute, $second, $microsecond);
    }

    public function __toString(): string
    {
        return $this->format(self::DEFAULT_FORMAT);
    }

    /**
     * Formats this value.
     *
     * @see \DateTimeInterface::format()
     */
    public function format(string $format): string
    {
        return $this->delegate->format($format);
    }

    /**
     * Returns the date component of this value.
     */
    public function toDate(): LocalDate
    {
        return LocalDate::from($this->delegate);
    }

    /**
     * Returns the time component of this value.
     */
    public function toTime(): LocalTime
    {
        return LocalTime::from($this->delegate);
    }
}
