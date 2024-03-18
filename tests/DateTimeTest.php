<?php

namespace Test\ICanBoogie\DateTime;

use ICanBoogie\DateTime\LocalDate;
use ICanBoogie\DateTime\LocalDateTime;
use ICanBoogie\DateTime\LocalTime;
use ICanBoogie\DateTime\TimeZone;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class DateTimeTest extends TestCase
{
    use IntervalTrait;

    private LocalDateTime $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sut = LocalDateTime::from(SAMPLE_DATETIME_VALUE);
    }

    #[DataProvider("provideFromValue")]
    public function testFromString(string|\DateTimeInterface $value): void
    {
        $actual = LocalDateTime::from($value);

        $this->assertEquals(SAMPLE_YEAR, $actual->year);
        $this->assertEquals(SAMPLE_MONTH, $actual->month);
        $this->assertEquals(SAMPLE_MONTH_NUMBER, $actual->monthNumber);
        $this->assertEquals(SAMPLE_DAY_OF_MONTH, $actual->dayOfMonth);
        $this->assertEquals(SAMPLE_DAY_OF_WEEK, $actual->dayOfWeek);
        $this->assertEquals(SAMPLE_DAY_OF_YEAR, $actual->dayOfYear);
        $this->assertEquals(SAMPLE_HOUR, $actual->hour);
        $this->assertEquals(SAMPLE_MINUTE, $actual->minute);
        $this->assertEquals(SAMPLE_SECOND, $actual->second);
        $this->assertEquals(SAMPLE_MICRO_SECOND, $actual->microsecond);
    }

    public static function provideFromValue(): array // @phpstan-ignore-line
    {
        return [
            [ SAMPLE_DATETIME_VALUE ],
            [ new \DateTime(SAMPLE_DATETIME_VALUE) ],
            [ new \DateTimeImmutable(SAMPLE_DATETIME_VALUE) ],
        ];
    }

    public function testDelegateIsEquivalent(): void
    {
        $actual = $this->sut->delegate;
        $expected = new \DateTimeImmutable(SAMPLE_DATETIME_VALUE, TimeZone::utc()->delegate);

        $this->assertInstanceOf(\DateTimeImmutable::class, $actual);
        $this->assertEquals(
            $expected->format(LocalDateTime::DEFAULT_FORMAT),
            $actual->format(LocalDateTime::DEFAULT_FORMAT),
        );
    }

    public function testToStringIsEquivalentToDefaultFormat(): void
    {
        $actual = (string)$this->sut;

        $this->assertEquals(SAMPLE_DATETIME_VALUE, $actual);
    }

    public function testFormat(): void
    {
        $actual = $this->sut->format("Y");

        $this->assertEquals(SAMPLE_YEAR, $actual);
    }

    public function testToDate(): void
    {
        $actual = $this->sut->toDate();

        $this->assertInstanceOf(LocalDate::class, $actual);
        $this->assertEquals(SAMPLE_YEAR, $actual->year);
        $this->assertEquals(SAMPLE_MONTH, $actual->month);
        $this->assertEquals(SAMPLE_MONTH_NUMBER, $actual->monthNumber);
        $this->assertEquals(SAMPLE_DAY_OF_MONTH, $actual->dayOfMonth);
    }

    public function testToTime(): void
    {
        $actual = $this->sut->toTime();

        $this->assertInstanceOf(LocalTime::class, $actual);
        $this->assertEquals(SAMPLE_HOUR, $actual->hour);
        $this->assertEquals(SAMPLE_MINUTE, $actual->minute);
        $this->assertEquals(SAMPLE_SECOND, $actual->second);
        $this->assertEquals(SAMPLE_MICRO_SECOND, $actual->microsecond);
    }

    /**
     * @see IntervalTrait::testArithmetic()
     * @see IntervalTrait::testCompareTo()
     */
    public static function provideInterval(): array // @phpstan-ignore-line
    {
        return [

            [ "P1Y" ],
            [ "P1M" ],
            [ "P1D" ],
            [ "PT1H" ],
            [ "PT1M" ],
            [ "PT1S" ],
            [ "P1YT1H" ],

        ];
    }
}
