<?php

namespace ICanBoogie\DateTime;

/**
 * Represents time-of-day without a referencing a specific date.
 */
readonly class LocalTime
{
    use LocalTimeTrait;
    use IntervalTrait;

    /**
     * The default format for this value.
     */
    public const DEFAULT_FORMAT = self::FORMAT_WITH_MICROSECONDS;

    public const FORMAT_WITH_MICROSECONDS = "H:i:s.u";
    public const FORMAT_WITHOUT_MICROSECONDS = "H:i:s";

    public static function from(string|\DateTimeInterface $value): self
    {
        $delegate = $value instanceof \DateTimeInterface
            ? $value
            : new \DateTimeImmutable($value);

        return new self(
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

    public function __construct(
        int $hour,
        int $minute,
        int $second = 0,
        int $microsecond = 0,
    ) {
        $this->delegate = $this->constructTime(
            $hour,
            $minute,
            $second,
            $microsecond,
        );
    }

    public function __toString(): string
    {
        return $this->format(self::DEFAULT_FORMAT);
    }

    public function format(string $format): string
    {
        return $this->delegate->format($format);
    }

    /**
     * Combines this time's components with the specified date components into a {@see LocalDateTime} value.
     */
    public function at(
        int $year,
        int|Month $monthOrNumber,
        int $dayOfMonth = 1,
    ): LocalDateTime {
        $monthNumber = $monthOrNumber instanceof Month
            ? $monthOrNumber->value
            : $monthOrNumber;

        $at = $this->delegate
            ->setDate($year, $monthNumber, $dayOfMonth);

        return LocalDateTime::from($at);
    }

    /**
     * Combines this time's components with the specified {@see LocalDate} value into a {@see LocalDateTime} value.
     */
    public function atDate(LocalDate $date): LocalDateTime
    {
        return $this->at($date->year, $date->month, $date->dayOfMonth);
    }
}
