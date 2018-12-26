<?php
declare(strict_types=1);

namespace App\Tests\App\Twig;

use App\Twig\Weekday;
use PHPUnit\Framework\TestCase;

class WeekdayTest extends TestCase
{
    /** @var Weekday */
    private $weekday;

    public function setUp()
    {
        $this->weekday = new Weekday();
    }

    public function testGetFunctions()
    {
        $this->assertInstanceOf(\Twig_SimpleFunction::class, $this->weekday->getFunctions()[0]);
    }

    public function testGetWeekdayInvalidDay()
    {
        $this->assertSame('-', $this->weekday->getWeekday(999));
    }

    public function testGetWeekdaySuccess()
    {
        $this->assertSame('schedule_list.weekday.monday', $this->weekday->getWeekday(1));
    }
}
