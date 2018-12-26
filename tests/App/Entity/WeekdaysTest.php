<?php
declare(strict_types=1);

namespace App\Tests\App\Entity;

use App\Entity\Weekdays;
use App\Exception\InvalidWeekdayException;
use PHPUnit\Framework\TestCase;

class WeekdaysTest extends TestCase
{
    public function testGetDaysOfTheWeek()
    {
        $this->assertContains(Weekdays::MONDAY, Weekdays::getDaysOfTheWeekKeys());
        $this->assertContains(Weekdays::TUESDAY, Weekdays::getDaysOfTheWeekKeys());
        $this->assertContains(Weekdays::WEDNESDAY, Weekdays::getDaysOfTheWeekKeys());
        $this->assertContains(Weekdays::THURSDAY, Weekdays::getDaysOfTheWeekKeys());
        $this->assertContains(Weekdays::FRIDAY, Weekdays::getDaysOfTheWeekKeys());
        $this->assertContains(Weekdays::SATURDAY, Weekdays::getDaysOfTheWeekKeys());
        $this->assertContains(Weekdays::SUNDAY, Weekdays::getDaysOfTheWeekKeys());
    }

    public function testGetDaysOfTheWeekKeys()
    {
        $this->assertSame([1, 2, 3, 4, 5, 6, 7], Weekdays::getDaysOfTheWeekKeys());
    }

    public function testValidateInvalidInput()
    {
        $this->assertFalse(Weekdays::validate(9));
    }

    public function testValidateSuccess()
    {
        $this->assertTrue(Weekdays::validate(3));
    }

    public function testGetDayOfTheWeekByIdInvalidInput()
    {
        $this->expectException(InvalidWeekdayException::class);
        $this->assertSame('monday', Weekdays::getDayOfTheWeekById(0));
    }

    public function testGetDayOfTheWeekByIdSuccess()
    {
        $this->assertSame('monday', Weekdays::getDayOfTheWeekById(1));
    }
}
