<?php
declare(strict_types=1);

namespace App\Tests\Service\StreamProcessing;

use App\Entity\StreamSchedule;
use App\Exception\ConflictingScheduledStreamsException;
use App\Exception\CouldNotModifyStreamScheduleException;
use App\Exception\CouldNotStartLivestreamException;
use App\Exception\ExecutorCouldNotExecuteStreamException;
use App\Repository\StreamScheduleRepository;
use App\Service\StreamProcessing\StartLivestream;
use App\Service\StreamProcessing\StopLivestream;
use App\Service\StreamProcessing\StreamScheduleExecutor;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @coversDefaultClass \App\Service\StreamProcessing\StreamScheduleExecutor
 * @covers ::<!public>
 * @covers ::__construct
 * @uses \App\Entity\StreamSchedule
 * @uses \App\Entity\ScheduleLog
 */
class StreamScheduleExecutorTest extends TestCase
{
    /** @var EntityManagerInterface|MockObject */
    private $entityManagerMock;

    /** @var StreamScheduleRepository|MockObject */
    private $streamScheduleRepositoryMock;

    /** @var LoggerInterface|MockObject */
    private $loggerMock;

    /** @var StopLivestream|MockObject */
    private $stopLivestreamMock;

    /** @var StartLivestream|MockObject */
    private $startLivestreamMock;

    /** @var StreamScheduleExecutor */
    private $streamScheduleExecutor;

    public function setUp()
    {
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->streamScheduleRepositoryMock = $this->createMock(StreamScheduleRepository::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->stopLivestreamMock = $this->createMock(StopLivestream::class);
        $this->startLivestreamMock = $this->createMock(StartLivestream::class);
        $this->streamScheduleExecutor = new StreamScheduleExecutor(
            $this->entityManagerMock,
            $this->streamScheduleRepositoryMock,
            $this->loggerMock,
            $this->stopLivestreamMock,
            $this->startLivestreamMock
        );
    }

    /**
     * @throws CouldNotModifyStreamScheduleException
     * @throws ExecutorCouldNotExecuteStreamException
     * @covers ::start
     */
    public function testStartSuccess()
    {
        $this->startLivestreamMock->expects($this->once())->method('process');
        $this->streamScheduleRepositoryMock->expects($this->once())->method('save');
        $this->streamScheduleExecutor->start(new StreamSchedule());
        $this->addToAssertionCount(1);
    }

    /**
     * @throws CouldNotModifyStreamScheduleException
     * @throws ExecutorCouldNotExecuteStreamException
     * @covers ::start
     */
    public function testStartFailed()
    {
        $this->expectException(ExecutorCouldNotExecuteStreamException::class);

        $this->startLivestreamMock->expects($this->once())
            ->method('process')
            ->willThrowException(CouldNotStartLivestreamException::hostNotAvailable());
        $this->streamScheduleRepositoryMock->expects($this->once())->method('save');

        $this->streamScheduleExecutor->start(new StreamSchedule());
    }

    /**
     * @throws CouldNotModifyStreamScheduleException
     * @throws ExecutorCouldNotExecuteStreamException
     * @covers ::stop
     */
    public function testStopSuccess()
    {
        $this->stopLivestreamMock->expects($this->once())->method('process');
        $this->streamScheduleRepositoryMock->expects($this->once())->method('save');
        $this->streamScheduleExecutor->stop(new StreamSchedule());
        $this->addToAssertionCount(1);
    }

    /**
     * @throws CouldNotModifyStreamScheduleException
     * @throws ExecutorCouldNotExecuteStreamException
     * @covers ::stop
     */
    public function testStopFailed()
    {
        $this->expectException(ExecutorCouldNotExecuteStreamException::class);

        $this->stopLivestreamMock->expects($this->once())
            ->method('process')
            ->willThrowException(new \InvalidArgumentException());
        $this->streamScheduleRepositoryMock->expects($this->once())->method('save');

        $this->streamScheduleExecutor->stop(new StreamSchedule());
    }
}
