<?php

namespace Test\ICanBoogie\DateTime;

use ICanBoogie\DateTime\LocalDate;
use ICanBoogie\DateTime\LocalDateTime;
use ICanBoogie\DateTime\Month;
use ICanBoogie\DateTime\LocalTime;
use ICanBoogie\DateTime\TimeZone;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class LocalDateTest extends TestCase
{
    use IntervalTrait;

    private LocalDate $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sut = LocalDate::from(SAMPLE_DATE_VALUE);
    }

    #[DataProvider("provideFromValue")]
    public function testFromString(string|\DateTimeInterface $value): void
    {
        $actual = LocalDate::from($value);

        $this->assertEquals(SAMPLE_YEAR, $actual->year);
        $this->assertEquals(SAMPLE_MONTH, $actual->month);
        $this->assertEquals(SAMPLE_MONTH_NUMBER, $actual->monthNumber);
        $this->assertEquals(SAMPLE_DAY_OF_MONTH, $actual->dayOfMonth);
        $this->assertEquals(SAMPLE_DAY_OF_WEEK, $actual->dayOfWeek);
        $this->assertEquals(SAMPLE_DAY_OF_YEAR, $actual->dayOfYear);
    }

    public static function provideFromValue(): array // @phpstan-ignore-line
    {
        return [
            [ SAMPLE_DATE_VALUE ],
            [ new \DateTime(SAMPLE_DATE_VALUE) ],
            [ new \DateTimeImmutable(SAMPLE_DATE_VALUE) ],
        ];
    }

    #[DataProvider("provideMonth")]
    public function testConstruct(int|Month $month): void
    {
        $actual = new LocalDate(SAMPLE_YEAR, $month, SAMPLE_DAY_OF_MONTH);

        $this->assertEquals(SAMPLE_YEAR, $actual->year);
        $this->assertEquals(SAMPLE_MONTH, $actual->month);
        $this->assertEquals(SAMPLE_MONTH_NUMBER, $actual->monthNumber);
        $this->assertEquals(SAMPLE_DAY_OF_MONTH, $actual->dayOfMonth);
    }

    public static function provideMonth(): array // @phpstan-ignore-line
    {
        return [
            [ SAMPLE_MONTH ],
            [ SAMPLE_MONTH_NUMBER ],
        ];
    }

    public function testDelegateIsEquivalent(): void
    {
        $actual = $this->sut->delegate;
        $expected = new \DateTimeImmutable(SAMPLE_DATE_VALUE, TimeZone::utc()->delegate);

        $this->assertInstanceOf(\DateTimeImmutable::class, $actual);
        $this->assertEquals(
            $expected->format(LocalDate::DEFAULT_FORMAT),
            $actual->format(LocalDate::DEFAULT_FORMAT),
        );
    }

    public function testToStringIsEquivalentToDefaultFormat(): void
    {
        $actual = (string)$this->sut;

        $this->assertEquals(SAMPLE_DATE_VALUE, $actual);
    }

    public function testFormat(): void
    {
        $actual = $this->sut->format("Y");

        $this->assertEquals(SAMPLE_YEAR, $actual);
    }

    public function testAt(): void
    {
        $actual = $this->sut->at(SAMPLE_HOUR, SAMPLE_MINUTE, SAMPLE_SECOND, SAMPLE_MICRO_SECOND);

        $this->assertInstanceOf(LocalDateTime::class, $actual);
        $this->assertEquals(SAMPLE_YEAR, $actual->year);
        $this->assertEquals(SAMPLE_MONTH, $actual->month);
        $this->assertEquals(SAMPLE_MONTH_NUMBER, $actual->monthNumber);
        $this->assertEquals(SAMPLE_DAY_OF_MONTH, $actual->dayOfMonth);
        $this->assertEquals(SAMPLE_HOUR, $actual->hour);
        $this->assertEquals(SAMPLE_MINUTE, $actual->minute);
        $this->assertEquals(SAMPLE_SECOND, $actual->second);
        $this->assertEquals(SAMPLE_MICRO_SECOND, $actual->microsecond);
    }

    public function testAtTime(): void
    {
        $time = LocalTime::from(SAMPLE_TIME_VALUE);
        $actual = $this->sut->atTime($time);

        $this->assertInstanceOf(LocalDateTime::class, $actual);
        $this->assertEquals(SAMPLE_YEAR, $actual->year);
        $this->assertEquals(SAMPLE_MONTH, $actual->month);
        $this->assertEquals(SAMPLE_MONTH_NUMBER, $actual->monthNumber);
        $this->assertEquals(SAMPLE_DAY_OF_MONTH, $actual->dayOfMonth);
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

        ];
    }
}
