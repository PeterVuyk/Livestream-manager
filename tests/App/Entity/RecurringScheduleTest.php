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

    /**
     * @throws \Exception
     */
    public function testLastExecution()
    {
        $recurringSchedule = new RecurringSchedule();
        $recurringSchedule->setLastExecution(new \DateTime());
        $this->assertInstanceOf(\DateTime::class, $recurringSchedule->getLastExecution());
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

    public function testExecutionTimeMinutes()
    {
        $recurringSchedule = new RecurringSchedule();
        $recurringSchedule->setExecutionTime(new \DateTime());
        $this->assertInstanceOf(\DateTime::class, $recurringSchedule->getExecutionTime());
    }

    public function testExecutionDayOfTheWeek()
    {
        $recurringSchedule = new RecurringSchedule();
        $recurringSchedule->setExecutionDay('monday');
        $this->assertSame('monday', $recurringSchedule->getExecutionDay());
    }

    public function testGetNextExecutionTime()
    {
        $recurringSchedule = new RecurringSchedule();
        $recurringSchedule->setExecutionTime(new \DateTime());
        $recurringSchedule->setExecutionDay('monday');
        $this->assertInstanceOf(\DateTime::class, $recurringSchedule->getNextExecutionTime());
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

    /**
     * @dataProvider streamTobeExecutedProvider
     * @param RecurringSchedule $recurringSchedule
     * @param bool $result
     */
    public function testStreamTobeExecuted(RecurringSchedule $recurringSchedule, bool $result)
    {
        $this->assertSame($result, $recurringSchedule->streamTobeExecuted());
    }

    public function streamTobeExecutedProvider()
    {
        $recurringScheduleWrecked = new RecurringSchedule();
        $recurringScheduleWrecked->setWrecked(true);
        $recurringScheduleRunWithNextExecution = new RecurringSchedule();
        $recurringScheduleRunWithNextExecution->setRunWithNextExecution(true);
        $recurringScheduleNextExecution = new RecurringSchedule();
        $recurringScheduleNextExecution->setExecutionTime(new \DateTime('- 1 minute'));
        $recurringScheduleNoExecution = new RecurringSchedule();
        $recurringScheduleNoExecution->setExecutionTime(new \DateTime('+ 1 minute'));
        $recurringScheduleNoExecution->setExecutionDay(date('l'));
        $recurringScheduleAlreadyExecuted = new RecurringSchedule();
        $recurringScheduleAlreadyExecuted->setLastExecution(new \DateTime());
        $recurringScheduleAlreadyExecuted->setExecutionDay(date('l'));

        return [
            [
                'recurringSchedule' => $recurringScheduleWrecked,
                'result' => false,
            ], [
                'recurringSchedule' => $recurringScheduleRunWithNextExecution,
                'result' => true,
            ], [
                'recurringSchedule' => $recurringScheduleAlreadyExecuted,
                'result' => false,
            ], [
                'recurringSchedule' => $recurringScheduleNextExecution,
                'result' => true,
            ], [
                'recurringSchedule' => $recurringScheduleNoExecution,
                'result' => false,
            ]
        ];
    }
}
