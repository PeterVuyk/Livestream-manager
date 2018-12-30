<?php
declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\ScheduleLog;
use App\Entity\StreamSchedule;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @coversDefaultClass \App\Entity\ScheduleLog
 * @covers ::<!public>
 * @covers ::__construct
 * @uses \App\Entity\StreamSchedule
 */
class ScheduleLogTest extends TestCase
{
    /**
     * @throws \Exception
     * @covers ::getId
     */
    public function testGetId()
    {
        $this->assertInstanceOf(Uuid::class, $this->getScheduleLog()->getid());
    }

    /**
     * @throws \Exception
     * @covers ::getStreamSchedule
     */
    public function testGetStreamSchedule()
    {
        $this->assertInstanceOf(StreamSchedule::class, $this->getScheduleLog()->getStreamSchedule());
    }

    /**
     * @throws \Exception
     * @covers ::getMessage
     */
    public function testGetMessage()
    {
        $this->assertSame('message', $this->getScheduleLog()->getMessage());
    }

    /**
     * @throws \Exception
     * @covers ::getTimeExecuted
     */
    public function testGetTimeExecuted()
    {
        $this->assertInstanceOf(\DateTimeInterface::class, $this->getScheduleLog()->getTimeExecuted());
    }

    /**
     * @throws \Exception
     * @covers ::getRunSuccessful
     */
    public function testRunSuccessful()
    {
        $this->assertSame(true, $this->getScheduleLog()->getRunSuccessful());
    }

    /**
     * @throws \Exception
     * @return ScheduleLog
     */
    private function getScheduleLog()
    {
        return new ScheduleLog(new StreamSchedule(), true, 'message');
    }
}
