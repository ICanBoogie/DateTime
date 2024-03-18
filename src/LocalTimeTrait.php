<?php

namespace ICanBoogie\DateTime;

trait LocalTimeTrait
{
    /**
     * The hour component of this value.
     */
    public readonly int $hour;

    /**
     * The minute component of this value.
     */
    public readonly int $minute;

    /**
     * The second component of this value.
     */
    public readonly int $second;

    /**
     * The microsecond component of this value.
     */
    public readonly int $microsecond;

    private function constructTime(
        int $hour,
        int $minute,
        int $second = 0,
        int $microsecond = 0,
    ): \DateTimeImmutable {
        $this->hour = $hour;
        $this->minute = $minute;
        $this->second = $second;
        $this->microsecond = $microsecond;

        return (new \DateTimeImmutable(timezone: TimeZone::utc()->delegate))
            ->setDate(0, 0, 0)
            ->setTime($hour, $minute, $second, $microsecond);
    }

    /**
     * Returns the time as a second of a day, in `0 until 24 * 60 * 60`.
     */
    public function toSecondOfDay(): int
    {
        return $this->hour * 60 * 60 + $this->minute * 60 + $this->second;
    }

    /**
     * Returns the time as a millisecond of a day, in `0 until 24 * 60 * 60 * 1_000`.
     */
    public function toMillisecondOfDay(): int
    {
        return $this->toSecondOfDay() * 1_000 + (int)($this->microsecond / 1_000);
    }

    /**
     * Returns the time as a microsecond of a day, in `0 until 24 * 60 * 60 * 1_000_000`.
     */
    public function toMicrosecondOfDay(): int
    {
        return $this->toSecondOfDay() * 1_000_000 + $this->microsecond;
    }
}
