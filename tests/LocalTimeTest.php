<?php

namespace Test\ICanBoogie\DateTime;

use ICanBoogie\DateTime\LocalDate;
use ICanBoogie\DateTime\LocalDateTime;
use ICanBoogie\DateTime\Month;
use ICanBoogie\DateTime\LocalTime;
use ICanBoogie\DateTime\TimeZone;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class LocalTimeTest extends TestCase
{
    use IntervalTrait;

    private LocalTime $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sut = LocalTime::from(SAMPLE_TIME_VALUE);
    }

    #[DataProvider("provideFromValue")]
    public function testFromString(string|\DateTimeInterface $value): void
    {
        $actual = LocalTime::from($value);

        $this->assertEquals(SAMPLE_HOUR, $actual->hour);
        $this->assertEquals(SAMPLE_MINUTE, $actual->minute);
        $this->assertEquals(SAMPLE_SECOND, $actual->second);
        $this->assertEquals(SAMPLE_MICRO_SECOND, $actual->microsecond);
    }

    public static function provideFromValue(): array // @phpstan-ignore-line
    {
        return [
            [ SAMPLE_TIME_VALUE ],
            [ new \DateTime(SAMPLE_TIME_VALUE) ],
            [ new \DateTimeImmutable(SAMPLE_TIME_VALUE) ],
        ];
    }

    public function testConstructor(): void
    {
        $actual = new LocalTime(SAMPLE_HOUR, SAMPLE_MINUTE, SAMPLE_SECOND, SAMPLE_MICRO_SECOND);

        $this->assertEquals(SAMPLE_HOUR, $actual->hour);
        $this->assertEquals(SAMPLE_MINUTE, $actual->minute);
        $this->assertEquals(SAMPLE_SECOND, $actual->second);
        $this->assertEquals(SAMPLE_MICRO_SECOND, $actual->microsecond);
    }

    public function testDelegateIsEquivalent(): void
    {
        $actual = $this->sut->delegate;
        $expected = new \DateTimeImmutable(SAMPLE_TIME_VALUE, TimeZone::utc()->delegate);

        $this->assertInstanceOf(\DateTimeImmutable::class, $actual);
        $this->assertEquals(
            $expected->format(LocalTime::DEFAULT_FORMAT),
            $actual->format(LocalTime::DEFAULT_FORMAT),
        );
    }

    public function testToStringIsEquivalentToDefaultFormat(): void
    {
        $actual = (string)$this->sut;

        $this->assertEquals(SAMPLE_TIME_VALUE, $actual);
    }

    public function testFormat(): void
    {
        $actual = $this->sut->format("i");

        $this->assertEquals(SAMPLE_MINUTE, $actual);
    }

    #[DataProvider("provideMonth")]
    public function testAt(int|Month $month): void
    {
        $actual = $this->sut->at(SAMPLE_YEAR, $month, SAMPLE_DAY_OF_MONTH);

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

    public static function provideMonth(): array // @phpstan-ignore-line
    {
        return [
            [ SAMPLE_MONTH ],
            [ SAMPLE_MONTH_NUMBER ],
        ];
    }

    public function testAtDate(): void
    {
        $date = LocalDate::from(SAMPLE_DATE_VALUE);
        $actual = $this->sut->atDate($date);

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

    public function testToSecondOfDay(): void
    {
        $actual = $this->sut->toSecondOfDay();

        $this->assertEquals(SAMPLE_SECONDS_OF_DAY, $actual);
    }

    public function testMillisecondOfDay(): void
    {
        $actual = $this->sut->toMillisecondOfDay();

        $this->assertEquals((int)(SAMPLE_MICRO_SECOND / 1_000) + SAMPLE_SECONDS_OF_DAY * 1_000, $actual);
    }

    public function testMicrosecondOfDay(): void
    {
        $actual = $this->sut->toMicrosecondOfDay();

        $this->assertEquals(SAMPLE_MICRO_SECOND + SAMPLE_SECONDS_OF_DAY * 1_000_000, $actual);
    }

    /**
     * @see IntervalTrait::testArithmetic()
     * @see IntervalTrait::testCompareTo()
     */
    public static function provideInterval(): array // @phpstan-ignore-line
    {
        return [

            [ "PT1H" ],
            [ "PT2M" ],
            [ "PT3S" ],

        ];
    }
}
