<?php
declare(strict_types=1);

namespace App\Tests\App\Entity;

use App\Entity\ScheduleLog;
use App\Entity\StreamSchedule;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class ScheduleLogTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testGetId()
    {
        $this->assertInstanceOf(Uuid::class, $this->getScheduleLog()->getid());
    }

    /**
     * @throws \Exception
     */
    public function testGetStreamSchedule()
    {
        $this->assertInstanceOf(StreamSchedule::class, $this->getScheduleLog()->getStreamSchedule());
    }

    /**
     * @throws \Exception
     */
    public function testGetMessage()
    {
        $this->assertSame('message', $this->getScheduleLog()->getMessage());
    }

    /**
     * @throws \Exception
     */
    public function testGetTimeExecuted()
    {
        $this->assertInstanceOf(\DateTimeInterface::class, $this->getScheduleLog()->getTimeExecuted());
    }

    /**
     * @throws \Exception
     */
    public function testRunSuccessful()
    {
        $this->assertSame(true, $this->getScheduleLog()->getRunSuccessful());
    }

    /**
     * @throws \Exception
     * @return ScheduleLog
     */
    public function getScheduleLog()
    {
        return new ScheduleLog(new StreamSchedule(), true, 'message');
    }
}
