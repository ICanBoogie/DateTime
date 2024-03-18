<?php

namespace ICanBoogie\DateTime;

/**
 * A trait for values that support interval operations.
 */
trait IntervalTrait
{
    /**
     * Returns a value that is the result of adding the {@see \DateInterval} to this value.
     */
    public function add(\DateInterval $interval): self
    {
        return self::from($this->delegate->add($interval));
    }

    /**
     * Returns a value that is the result of subtracting the {@see \DateInterval} to this value.
     */
    public function sub(\DateInterval $interval): self
    {
        return self::from($this->delegate->sub($interval));
    }

    /**
     * Compares this value with `$other` and returns the interval.
     */
    public function compareTo(self $other, bool $absolute = false): \DateInterval
    {
        return $this->delegate->diff($other->delegate, $absolute);
    }
}
