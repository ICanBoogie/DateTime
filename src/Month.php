<?php

namespace ICanBoogie\DateTime;

enum Month : int
{
    /**
     * Month #1, with 31 days.
     */
    case JANUARY = 1;

    /**
     * Month #2, with 28 days, or 29 in leap years.
     */
    case FEBRUARY = 2;

    /**
     * Month #3, with 31 days.
     */
    case MARCH = 3;

    /**
     * Month #4, with 30 days.
     */
    case APRIL = 4;

    /**
     * Month #5, with 31 days.
     */
    case MAY = 5;

    /**
     * Month #6, with 30 days.
     */
    case JUNE = 6;

    /**
     * Month #7, with 31 days.
     */
    case JULY = 7;

    /**
     * Month #8, with 31 days.
     */
    case AUGUST = 8;

    /**
     * Month #9, with 30 days.
     */
    case SEPTEMBER = 9;

    /**
     * Month #10, with 31 days.
     */
    case OCTOBER = 10;

    /**
     * Month #11, with 30 days.
     */
    case NOVEMBER = 11;

    /**
     * Month #12, with 31 days.
     */
    case DECEMBER = 12;
}
