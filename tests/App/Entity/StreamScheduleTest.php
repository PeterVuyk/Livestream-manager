<?php
declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\ScheduleLog;
use App\Entity\StreamSchedule;
use App\Exception\StreamSchedule\InvalidWeekdayException;
use App\Entity\Weekday;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Entity\StreamSchedule
 * @covers ::<!public>
 * @uses \App\Entity\Weekday
 * @uses \App\Entity\StreamSchedule
 * @uses \App\Entity\ScheduleLog
 */
class StreamScheduleTest extends TestCase
{
    /** @var StreamSchedule */
    private $streamSchedule;

    public function setUp()
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
        $streamSchedule->setChannel('channel-name');
        $streamSchedule->setExecutionDay(1);
        $scheduleLog = new ScheduleLog(new StreamSchedule(), true, 'message');
        $streamSchedule->addScheduleLog($scheduleLog);

        $this->streamSchedule = $streamSchedule;
    }

    /**
     * @covers ::getId
     * @covers ::setId
     */
    public function testId()
    {
        $this->assertSame('f1f28b0c-9ec1-47ab-86e6-af24a50293c1', $this->streamSchedule->getId());
    }

    /**
     * @covers ::setName
     * @covers ::getName
     */
    public function testName()
    {
        $this->assertSame('some-name', $this->streamSchedule->getName());
    }

    /**
     * @throws \Exception
     * @covers ::setLastExecution
     * @covers ::getLastExecution
     */
    public function testLastExecution()
    {
        $this->assertInstanceOf(\DateTime::class, $this->streamSchedule->getLastExecution());
    }

    /**
     * @covers ::setDisabled
     * @covers ::getDisabled
     */
    public function testDisabled()
    {
        $this->assertSame(false, $this->streamSchedule->getDisabled());
    }

    /**
     * @covers ::setWrecked
     * @covers ::isWrecked
     */
    public function testWrecked()
    {
        $this->assertSame(false, $this->streamSchedule->isWrecked());
    }

    /**
     * @covers ::getExecutionTime
     * @covers ::setExecutionTime
     */
    public function testExecutionTime()
    {
        $this->assertInstanceOf(\DateTime::class, $this->streamSchedule->getExecutionTime());
    }

    /**
     * @covers ::setIsRunning
     * @covers ::isRunning
     */
    public function testIsRunning()
    {
        $this->assertTrue($this->streamSchedule->isRunning());
    }

    /**
     * @covers ::getOnetimeExecutionDate
     * @covers ::setOnetimeExecutionDate
     */
    public function testGetOnetimeExecutionDate()
    {
        $this->assertInstanceOf(\DateTime::class, $this->streamSchedule->getOnetimeExecutionDate());
    }

    /**
     * @covers ::setExecutionDay
     * @covers ::getExecutionDay
     */
    public function testExecutionDaySuccess()
    {
        $this->assertSame(1, $this->streamSchedule->getExecutionDay());
    }

    /**
     * @covers ::setExecutionDay
     */
    public function testSetExecutionDayFailed()
    {
        $this->expectException(InvalidWeekdayException::class);
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setExecutionDay(999);
    }

    /**
     * @covers ::setChannel
     * @covers ::getChannel
     */
    public function testChannel()
    {
        $this->assertSame('channel-name', $this->streamSchedule->getChannel());
    }

    /**
     * @param StreamSchedule $streamSchedule
     * @param mixed|null $result
     * @dataProvider getNextExecutionTimeProvider
     * @covers ::getNextExecutionTime
     */
    public function testGetNextExecutionTime(StreamSchedule $streamSchedule, $result)
    {
        if ($result === null) {
            $this->assertSame($result, $streamSchedule->getNextExecutionTime());
        } elseif (is_string($result)) {
            $this->assertInstanceOf($result, $streamSchedule->getNextExecutionTime());
        }
    }

    public function getNextExecutionTimeProvider()
    {
        $streamSchedule1 = new StreamSchedule();
        $streamSchedule1->setExecutionTime(new \DateTime());
        $streamSchedule1->setExecutionDay(Weekday::THURSDAY);

        $streamSchedule2 = new StreamSchedule();
        $streamSchedule2->setExecutionTime(new \DateTime());

        $streamSchedule3 = new StreamSchedule();
        $streamSchedule3->setOnetimeExecutionDate(new \DateTime());

        return [
            [
                'streamSchedule' => $streamSchedule1,
                'result' => \DateTime::class,
            ], [
                'streamSchedule' => $streamSchedule2,
                'result' => null,
            ], [
                'streamSchedule' => $streamSchedule3,
                'result' => \DateTime::class,
            ]
        ];
    }

    /**
     * @covers ::getStreamDuration
     * @covers ::setStreamDuration
     */
    public function testStreamSchedule()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setStreamDuration(3);
        $this->assertSame(3, $streamSchedule->getStreamDuration());
    }

    /**
     * @dataProvider isRecurringProvider
     * @param StreamSchedule $streamSchedule
     * @param bool $isRecurring
     * @covers ::isRecurring
     */
    public function testIsRecurring(StreamSchedule $streamSchedule, bool $isRecurring)
    {
        $this->assertSame($isRecurring, $streamSchedule->isRecurring());
    }

    public function isRecurringProvider()
    {
        $onetimeSchedule = new StreamSchedule();
        $onetimeSchedule->setOnetimeExecutionDate(new \DateTime());
        return [
            [
                'streamSchedule' => new StreamSchedule(),
                'isRecurring' => true,
            ], [
                'streamSchedule' => $onetimeSchedule,
                'isRecurring' => false,
            ]
        ];
    }

    /**
     * @throws \Exception
     * @covers ::addScheduleLog
     * @covers ::getScheduleLog
     */
    public function testScheduleLog()
    {
        $scheduleLog = new ScheduleLog(new StreamSchedule(), true, 'message');
        $streamSchedule = new StreamSchedule();
        $streamSchedule->addScheduleLog($scheduleLog);
        $this->assertInstanceOf(ScheduleLog::class, $streamSchedule->getScheduleLog()[0]);
    }

    /**
     * @dataProvider streamTobeStartedProvider
     * @param StreamSchedule $streamSchedule
     * @param bool $result
     * @covers ::streamTobeStarted
     */
    public function testStreamTobeStarted(StreamSchedule $streamSchedule, bool $result)
    {
        $this->assertSame($result, $streamSchedule->streamTobeStarted());
    }

    public function streamTobeStartedProvider()
    {
        $daysOfTheWeek = [
            'Monday' => 1,
            'Tuesday' => 2,
            'Wednesday' => 3,
            'Thursday' => 4,
            'Friday' => 5,
            'Saturday' => 6,
            'Sunday' => 7,
        ];

        $streamScheduleWrecked = new StreamSchedule();
        $streamScheduleWrecked->setWrecked(true);
        $streamScheduleNextExecution = new StreamSchedule();
        $streamScheduleNextExecution->setExecutionTime(new \DateTime('- 1 minute'));
        $streamScheduleNextExecution->setExecutionDay($daysOfTheWeek[date('l')]);
        $streamScheduleNoExecution = new StreamSchedule();
        $streamScheduleNoExecution->setExecutionTime(new \DateTime('+ 1 minute'));
        $streamScheduleNoExecution->setExecutionDay($daysOfTheWeek[date('l')]);
        $streamScheduleAlreadyExecuted = new StreamSchedule();
        $streamScheduleAlreadyExecuted->setLastExecution(new \DateTime());
        $streamScheduleAlreadyExecuted->setExecutionDay($daysOfTheWeek[date('l')]);

        return [
            [
                'streamSchedule' => $streamScheduleWrecked,
                'result' => false,
            ], [
                'streamSchedule' => $streamScheduleAlreadyExecuted,
                'result' => false,
            ], [
                'streamSchedule' => $streamScheduleNextExecution,
                'result' => true,
            ], [
                'streamSchedule' => $streamScheduleNoExecution,
                'result' => false,
            ]
        ];
    }

    /**
     * @dataProvider getExecutionEndTimeProvider
     * @param StreamSchedule $streamSchedule
     * @param $result
     * @throws \Exception
     * @covers ::getExecutionEndTime
     */
    public function testGetExecutionEndTime(StreamSchedule $streamSchedule, $result)
    {
        $executionEndTime = null;
        if ($streamSchedule->getExecutionEndTime() instanceof \DateTimeInterface) {
            $executionEndTime = $streamSchedule->getExecutionEndTime()->format('H:i');
        }
        $this->assertEquals($result, $executionEndTime);
    }

    public function getExecutionEndTimeProvider()
    {
        $streamScheduleNotRunning = new StreamSchedule();
        $streamScheduleNotRunning->setWrecked(false);
        $streamScheduleNotRunning->setIsRunning(false);
        $streamScheduleNoStreamDuration = new StreamSchedule();
        $streamScheduleNoStreamDuration->setWrecked(false);
        $streamScheduleNoStreamDuration->setIsRunning(true);
        $streamScheduleWithEndTime = new StreamSchedule();
        $streamScheduleWithEndTime->setStreamDuration(5);
        $streamScheduleWithEndTime->setLastExecution((new \DateTime())->modify('-5 minutes'));

        return [
            [
                'streamSchedule' => $streamScheduleNotRunning,
                'result' => null,
            ], [
                'streamSchedule' => $streamScheduleNoStreamDuration,
                'result' => null,
            ], [
                'streamSchedule' => $streamScheduleWithEndTime,
                'result' => (new \DateTime())->format('H:i'),
            ]
        ];
    }

    /**
     * @dataProvider streamToBeStoppedProvider
     * @param bool $expectedResult
     * @param StreamSchedule $streamSchedule
     * @covers ::streamToBeStopped
     */
    public function testStreamToBeStopped(bool $expectedResult, StreamSchedule $streamSchedule)
    {
        $this->assertSame($expectedResult, $streamSchedule->streamToBeStopped());
    }

    public function streamToBeStoppedProvider()
    {
        $streamScheduleNotRunning = new StreamSchedule();
        $streamScheduleNotRunning->setIsRunning(false);
        $streamScheduleToBeStopped = new StreamSchedule();
        $streamScheduleToBeStopped->setIsRunning(true);
        $streamScheduleToBeStopped->setStreamDuration(5);
        $streamScheduleToBeStopped->setLastExecution(new \DateTime('- 10 minutes'));

        return [
            [
                'result' => false,
                'streamSchedule' => $streamScheduleNotRunning,
            ], [
                'result' => false,
                'streamSchedule' => new StreamSchedule(),
            ], [
                'result' => true,
                'streamSchedule' => $streamScheduleToBeStopped,
            ]
        ];
    }
}
