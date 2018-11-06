<?php
declare(strict_types=1);

namespace App\Tests\App\Entity;

use App\Entity\ScheduleLog;
use App\Entity\StreamSchedule;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class StreamScheduleTest extends TestCase
{
    public function testId()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setId('id-4');
        $this->assertSame('id-4', $streamSchedule->getId());
    }

    public function testName()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setName('some-name');
        $this->assertSame('some-name', $streamSchedule->getName());
    }

    public function testCommand()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setCommand('command:name');
        $this->assertSame('command:name', $streamSchedule->getCommand());
    }

    public function testCronExpression()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setCronExpression('* * * * *');
        $this->assertSame('* * * * *', $streamSchedule->getCronExpression());
    }

    /**
     * @throws \Exception
     */
    public function testLastExecution()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setLastExecution(new \DateTimeImmutable());
        $this->assertInstanceOf(\DateTimeImmutable::class, $streamSchedule->getLastExecution());
    }

    public function testPriority()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setPriority(50);
        $this->assertSame(50, $streamSchedule->getPriority());
    }

    public function testRunWithNextExecution()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setRunWithNextExecution(true);
        $this->assertSame(true, $streamSchedule->getRunWithNextExecution());
    }

    public function testDisabled()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setDisabled(false);
        $this->assertSame(false, $streamSchedule->getDisabled());
    }

    public function testWrecked()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setWrecked(false);
        $this->assertSame(false, $streamSchedule->isWrecked());
    }

    /**
     * @throws \Exception
     */
    public function testScheduleLog()
    {
        $scheduleLog = new ScheduleLog('4227813d-5c7b-4757-960e-a54a8c4cb67f', new StreamSchedule(), 'message');
        $streamSchedule = new StreamSchedule();
        $streamSchedule->addScheduleLog(new ArrayCollection([$scheduleLog]));
        $this->assertInstanceOf(ArrayCollection::class, $streamSchedule->getScheduleLog());
    }
}
