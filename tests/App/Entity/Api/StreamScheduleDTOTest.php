<?php
declare(strict_types=1);

namespace App\Tests\Entity\Api;

use App\Entity\Api\StreamScheduleDTO;
use App\Entity\StreamSchedule;
use App\Exception\StreamSchedule\CouldNotCreateStreamScheduleDTOException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Entity\Api\StreamScheduleDTO
 * @covers ::<!public>
 * @covers ::__construct()
 * @uses \App\Entity\Weekday
 * @uses \App\Entity\StreamSchedule
 * @uses \App\Entity\Api\StreamScheduleDTO
 */
class StreamScheduleDTOTest extends TestCase
{
    /** @var StreamScheduleDTO */
    private $streamScheduleDTO;

    public function setUp()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setId('f1f28b0c-9ec1-47ab-86e6-af24a50293c1');
        $streamSchedule->setName('some-name');
        $streamSchedule->setLastExecution(new \DateTime());
        $streamSchedule->setDisabled(false);
        $streamSchedule->setExecutionTime(new \DateTime());
        $streamSchedule->setStreamDuration(4);
        $streamSchedule->setIsRunning(true);
        $streamSchedule->setOnetimeExecutionDate(new \DateTime());
        $streamSchedule->setExecutionDay(1);
        $this->streamScheduleDTO = StreamScheduleDTO::createFromStreamSchedule($streamSchedule);
    }

    /**
     * @covers ::getId
     */
    public function testGetId()
    {
        $this->assertSame('f1f28b0c-9ec1-47ab-86e6-af24a50293c1', $this->streamScheduleDTO->getId());
    }

    /**
     * @covers ::getName
     */
    public function testGetName()
    {
        $this->assertSame('some-name', $this->streamScheduleDTO->getName());
    }

    /**
     * @covers ::getExecutionDay
     */
    public function testGetExecutionDay()
    {
        $this->assertSame('monday', $this->streamScheduleDTO->getExecutionDay());
    }

    /**
     * @covers ::getExecutionTime
     */
    public function testGetExecutionTime()
    {
        $this->assertStringContainsString((new \DateTime())->format('H:i'), $this->streamScheduleDTO->getExecutionTime());
    }

    /**
     * @covers ::getOnetimeExecutionDate
     */
    public function testGetOnetimeExecutionDate()
    {
        $this->assertInstanceOf(\DateTime::class, $this->streamScheduleDTO->getOnetimeExecutionDate());
    }

    /**
     * ::@covers ::getMinutesStreamDuration
     */
    public function testGetMinutesStreamDuration()
    {
        $this->assertSame(4, $this->streamScheduleDTO->getMinutesStreamDuration());
    }

    /**
     * @covers ::isRunning
     */
    public function testIsRunning()
    {
        $this->assertTrue($this->streamScheduleDTO->isRunning());
    }

    /**
     * @covers ::isRecurring
     */
    public function testIsRecurring()
    {
        $this->assertFalse($this->streamScheduleDTO->isRecurring());
    }

    /**
     * @covers ::getNextExecutionTime
     */
    public function testGetNextExecutionTime()
    {
        $this->assertInstanceOf(\DateTime::class, $this->streamScheduleDTO->getNextExecutionTime());
    }

    /**
     * @covers ::grabPayload
     * @uses \App\Entity\Api\StreamScheduleDTO
     */
    public function testGetPayload()
    {
        $this->assertArrayHasKey('id', $this->streamScheduleDTO->grabPayload());
        $this->assertArrayHasKey('name', $this->streamScheduleDTO->grabPayload());
        $this->assertArrayHasKey('executionDay', $this->streamScheduleDTO->grabPayload());
        $this->assertArrayHasKey('executionTime', $this->streamScheduleDTO->grabPayload());
        $this->assertArrayHasKey('onetimeExecutionDate', $this->streamScheduleDTO->grabPayload());
        $this->assertArrayHasKey('minutesStreamDuration', $this->streamScheduleDTO->grabPayload());
        $this->assertArrayHasKey('isRunning', $this->streamScheduleDTO->grabPayload());
    }

    /**
     * @covers ::createFromStreamSchedule
     */
    public function testCreateFromStreamScheduleSuccess()
    {
        $this->assertInstanceOf(
            StreamScheduleDTO::class,
            StreamScheduleDTO::createFromStreamSchedule($this->getStreamSchedule())
        );
    }

    /**
     * @covers ::createFromStreamSchedule
     */
    public function testCreateFromStreamScheduleFailed()
    {
        $this->expectException(CouldNotCreateStreamScheduleDTOException::class);
        $streamSchedule = $this->getStreamSchedule();
        $streamSchedule->setStreamDuration(null);

        StreamScheduleDTO::createFromStreamSchedule($streamSchedule);
    }

    private function getStreamSchedule()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setId('f1f28b0c-9ec1-47ab-86e6-af24a50293c1');
        $streamSchedule->setName('some-name');
        $streamSchedule->setLastExecution(new \DateTime());
        $streamSchedule->setDisabled(false);
        $streamSchedule->setWrecked(false);
        $streamSchedule->setExecutionTime(new \DateTime());
        $streamSchedule->setIsRunning(true);
        $streamSchedule->setOnetimeExecutionDate(new \DateTime());
        $streamSchedule->setStreamDuration(4);
        $streamSchedule->setExecutionDay(1);
        return $streamSchedule;
    }
}
