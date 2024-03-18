<?php

namespace ICanBoogie\DateTime;

/**
 * A date-of-week in the ISO-8601 calendar system.
 */
enum DayOfWeek : int
{
    case MONDAY = 1;
    case TUESDAY = 2;
    case WEDNESDAY = 3;
    case THURSDAY = 4;
    case FRIDAY = 5;
    case SATURDAY = 6;
    case SUNDAY = 7;
}
