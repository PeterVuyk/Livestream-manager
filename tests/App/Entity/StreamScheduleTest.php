<?php
declare(strict_types=1);

namespace App\Tests\App\Entity;

use App\Entity\StreamSchedule;
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

    public function testLastRunSuccessful()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setLastRunSuccessful(true);
        $this->assertSame(true, $streamSchedule->getLastRunSuccessful());
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
}
