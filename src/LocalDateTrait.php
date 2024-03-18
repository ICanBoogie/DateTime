<?php

namespace ICanBoogie\DateTime;

trait LocalDateTrait
{
    /**
     * The year component of this value.
     */
    public readonly int $year;

    /**
     * The number-of-month (1..12) component of this value.
     */
    public readonly int $monthNumber;

    /**
     * The month component of this value.
     */
    public readonly Month $month;

    /**
     * The day-of-month (1..31) component of this value.
     */
    public readonly int $dayOfMonth;

    /**
     * The day-of-week (1..7) component of this value.
     */
    public readonly DayOfWeek $dayOfWeek;

    /**
     * The day-of-year (1..365 or 1..366 on leap year) component of this value.
     */
    public readonly int $dayOfYear;

    /**
     * Whether the value is a leap year.
     */
    public readonly bool $isLeapYear;

    private function constructDate(
        int $year,
        int|Month $monthOrNumber,
        int $dayOfMonth,
    ): \DateTimeImmutable {
        $this->year = $year;

        if ($monthOrNumber instanceof Month) {
            $this->month = $monthOrNumber;
            $this->monthNumber = $monthOrNumber->value;
        } else {
            $this->month = Month::from($monthOrNumber);
            $this->monthNumber = $monthOrNumber;
        }

        $delegate = (new \DateTimeImmutable(timezone: TimeZone::utc()->delegate))
            ->setDate($year, $this->monthNumber, $dayOfMonth);

        $this->dayOfMonth = $dayOfMonth;
        $this->dayOfWeek = DayOfWeek::from($delegate->format('N'));
        $this->dayOfYear = 1 + (int) $delegate->format('z');
        $this->isLeapYear = (bool) $delegate->format('L');

        return $delegate;
    }
}
