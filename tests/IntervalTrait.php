<?php

namespace Test\ICanBoogie\DateTime;

use PHPUnit\Framework\Attributes\DataProvider;

trait IntervalTrait
{
    #[DataProvider("provideInterval")]
    public function testArithmetic(string $interval): void
    {
        $interval = new \DateInterval($interval);
        $sut = $this->sut;
        $before = $sut->sub($interval);
        $after = $sut->add($interval);

        $this->assertTrue($sut > $before);
        $this->assertTrue($sut < $after);
        $this->assertEquals(1, $sut <=> $before);
        $this->assertEquals(-1, $sut <=> $after);
    }

    #[DataProvider("provideInterval")]
    public function testCompareTo(string $interval): void
    {
        $interval = new \DateInterval($interval);
        $sut = $this->sut;

        // positive
        $other = $this->sut->add($interval);
        $actual = $sut->compareTo($other);
        $expected = $this->sut->delegate->diff($other->delegate);

        $this->assertEquals($expected, $actual);

        // negative
        $other = $this->sut->sub($interval);
        $actual = $sut->compareTo($other);
        $expected = $this->sut->delegate->diff($other->delegate);

        $this->assertEquals($expected, $actual);
    }
}
