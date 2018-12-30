<?php
declare(strict_types=1);

namespace App\Tests\Twig;

use App\Twig\WeekdayExtension;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Twig\WeekdayExtension
 * @covers ::<!public>
 * @uses \App\Entity\Weekday
 */
class WeekdayExtensionTest extends TestCase
{
    /** @var WeekdayExtension */
    private $weekdayExtension;

    public function setUp()
    {
        $this->weekdayExtension = new WeekdayExtension();
    }

    /**
     * @covers ::getFunctions
     */
    public function testGetFunctions()
    {
        $this->assertInstanceOf(\Twig_SimpleFunction::class, $this->weekdayExtension->getFunctions()[0]);
    }

    /**
     * @covers ::getWeekday
     */
    public function testGetWeekdayInvalidDay()
    {
        $this->assertSame('-', $this->weekdayExtension->getWeekday(999));
    }

    /**
     * @covers ::getWeekday
     */
    public function testGetWeekdaySuccess()
    {
        $this->assertSame('schedule_list.weekday.monday', $this->weekdayExtension->getWeekday(1));
    }
}
