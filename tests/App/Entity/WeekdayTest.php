<?php
declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Weekday;
use App\Exception\InvalidWeekdayException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Entity\Weekday
 * @covers ::<!public>
 */
class WeekdayTest extends TestCase
{
    /**
     * @covers ::getDaysOfTheWeek
     */
    public function testGetDaysOfTheWeek()
    {
        $this->assertContains('monday', Weekday::getDaysOfTheWeek());
        $this->assertContains('tuesday', Weekday::getDaysOfTheWeek());
        $this->assertContains('wednesday', Weekday::getDaysOfTheWeek());
        $this->assertContains('thursday', Weekday::getDaysOfTheWeek());
        $this->assertContains('friday', Weekday::getDaysOfTheWeek());
        $this->assertContains('saturday', Weekday::getDaysOfTheWeek());
        $this->assertContains('sunday', Weekday::getDaysOfTheWeek());
    }

    /**
     * @covers ::getDaysOfTheWeekKeys
     * @uses \App\Entity\Weekday
     */
    public function testGetDaysOfTheWeekKeys()
    {
        $this->assertSame([1, 2, 3, 4, 5, 6, 7], Weekday::getDaysOfTheWeekKeys());
    }

    /**
     * @covers ::validate
     * @uses \App\Entity\Weekday
     */
    public function testValidateInvalidInput()
    {
        $this->assertFalse(Weekday::validate(9));
    }

    /**
     * @covers ::validate
     * @uses \App\Entity\Weekday
     */
    public function testValidateSuccess()
    {
        $this->assertTrue(Weekday::validate(3));
    }

    /**
     * @covers ::getDayOfTheWeekById
     * @uses \App\Entity\Weekday
     */
    public function testGetDayOfTheWeekByIdInvalidInput()
    {
        $this->expectException(InvalidWeekdayException::class);
        $this->assertSame('monday', Weekday::getDayOfTheWeekById(0));
    }

    /**
     * @covers ::getDayOfTheWeekById
     * @uses \App\Entity\Weekday
     */
    public function testGetDayOfTheWeekByIdSuccess()
    {
        $this->assertSame('monday', Weekday::getDayOfTheWeekById(1));
    }
}
