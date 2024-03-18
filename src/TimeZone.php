<?php

namespace ICanBoogie\DateTime;

class TimeZone
{
    public const DEFAULT_TIME_ZONE = "UTC";

    public static function utc(): self
    {
        static $instance;

        return $instance ??= new self(self::DEFAULT_TIME_ZONE);
    }

    /**
     * The {@see \DateTimeZone} representation of this value.
     */
    public \DateTimeZone $delegate;

    public function __construct(string $timezone)
    {
        $this->delegate = new \DateTimeZone($timezone);
    }
}
