<?php
declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\ScheduleLog;
use App\Entity\StreamSchedule;
use App\Exception\InvalidWeekdayException;
use App\Entity\Weekdays;
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

    /**
     * @throws \Exception
     */
    public function testLastExecution()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setLastExecution(new \DateTime());
        $this->assertInstanceOf(\DateTime::class, $streamSchedule->getLastExecution());
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

    public function testExecutionTime()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setExecutionTime(new \DateTime());
        $this->assertInstanceOf(\DateTime::class, $streamSchedule->getExecutionTime());
    }

    public function testExecutionDaySuccess()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setExecutionDay(1);
        $this->assertSame(1, $streamSchedule->getExecutionDay());
    }

    public function testSetExecutionDayFailed()
    {
        $this->expectException(InvalidWeekdayException::class);
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setExecutionDay(999);
    }

    /**
     * @param StreamSchedule $streamSchedule
     * @param mixed|null $result
     * @dataProvider getNextExecutionTimeProvider
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
        $streamSchedule1->setExecutionDay(Weekdays::THURSDAY);

        $streamSchedule2 = new StreamSchedule();
        $streamSchedule2->setExecutionTime(new \DateTime());

        return [
            [
                'streamSchedule' => $streamSchedule1,
                'result' => \DateTime::class,
            ], [
                'streamSchedule' => $streamSchedule2,
                'result' => null,
            ],
        ];
    }

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
     */
    public function testScheduleLog()
    {
        $scheduleLog = new ScheduleLog(new StreamSchedule(), true, 'message');
        $streamSchedule = new StreamSchedule();
        $streamSchedule->addScheduleLog($scheduleLog);
        $this->assertInstanceOf(ScheduleLog::class, $streamSchedule->getScheduleLog()[0]);
    }

    /**
     * @dataProvider streamTobeExecutedProvider
     * @param StreamSchedule $streamSchedule
     * @param bool $result
     */
    public function testStreamTobeExecuted(StreamSchedule $streamSchedule, bool $result)
    {
        $this->assertSame($result, $streamSchedule->streamTobeExecuted());
    }

    public function streamTobeExecutedProvider()
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
     */
    public function testGetExecutionEndTime(StreamSchedule $streamSchedule, $result)
    {
        $this->assertEquals($result, $streamSchedule->getExecutionEndTime());
    }

    public function getExecutionEndTimeProvider()
    {
        $streamScheduleNotRunning = new StreamSchedule();
        $streamScheduleNotRunning->setWrecked(false);
        $streamScheduleNotRunning->setIsRunning(false);
        $streamScheduleNoStreamDuration = new StreamSchedule();
        $streamScheduleNoStreamDuration->setWrecked(false);
        $streamScheduleNoStreamDuration->setIsRunning(true);
        $now = new \DateTime();
        $streamScheduleWithEndTime = new StreamSchedule();
        $streamScheduleWithEndTime->setStreamDuration(5);
        $streamScheduleWithEndTime->setLastExecution(($now)->modify('-5 minutes'));

        return [
            [
                'streamSchedule' => $streamScheduleNotRunning,
                'result' => null,
            ], [
                'streamSchedule' => $streamScheduleNoStreamDuration,
                'result' => null,
            ], [
                'streamSchedule' => $streamScheduleWithEndTime,
                'result' => $now,
            ]
        ];
    }
}