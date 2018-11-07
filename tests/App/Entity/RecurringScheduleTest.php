<?php
declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\ScheduleLog;
use App\Entity\RecurringSchedule;
use PHPUnit\Framework\TestCase;

class RecurringScheduleTest extends TestCase
{
    public function testId()
    {
        $recurringSchedule = new RecurringSchedule();
        $recurringSchedule->setId('id-4');
        $this->assertSame('id-4', $recurringSchedule->getId());
    }

    public function testName()
    {
        $recurringSchedule = new RecurringSchedule();
        $recurringSchedule->setName('some-name');
        $this->assertSame('some-name', $recurringSchedule->getName());
    }

    public function testCommand()
    {
        $recurringSchedule = new RecurringSchedule();
        $recurringSchedule->setCommand('command:name');
        $this->assertSame('command:name', $recurringSchedule->getCommand());
    }

    public function testCronExpression()
    {
        $recurringSchedule = new RecurringSchedule();
        $recurringSchedule->setCronExpression('* * * * *');
        $this->assertSame('* * * * *', $recurringSchedule->getCronExpression());
    }

    /**
     * @throws \Exception
     */
    public function testLastExecution()
    {
        $recurringSchedule = new RecurringSchedule();
        $recurringSchedule->setLastExecution(new \DateTimeImmutable());
        $this->assertInstanceOf(\DateTimeImmutable::class, $recurringSchedule->getLastExecution());
    }

    public function testPriority()
    {
        $recurringSchedule = new RecurringSchedule();
        $recurringSchedule->setPriority(50);
        $this->assertSame(50, $recurringSchedule->getPriority());
    }

    public function testRunWithNextExecution()
    {
        $recurringSchedule = new RecurringSchedule();
        $recurringSchedule->setRunWithNextExecution(true);
        $this->assertSame(true, $recurringSchedule->getRunWithNextExecution());
    }

    public function testDisabled()
    {
        $recurringSchedule = new RecurringSchedule();
        $recurringSchedule->setDisabled(false);
        $this->assertSame(false, $recurringSchedule->getDisabled());
    }

    public function testWrecked()
    {
        $recurringSchedule = new RecurringSchedule();
        $recurringSchedule->setWrecked(false);
        $this->assertSame(false, $recurringSchedule->isWrecked());
    }

    /**
     * @throws \Exception
     */
    public function testScheduleLog()
    {
        $scheduleLog = new ScheduleLog(new RecurringSchedule(), true, 'message');
        $recurringSchedule = new RecurringSchedule();
        $recurringSchedule->addScheduleLog($scheduleLog);
        $this->assertInstanceOf(ScheduleLog::class, $recurringSchedule->getScheduleLog()[0]);
    }
}
