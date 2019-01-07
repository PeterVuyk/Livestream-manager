<?php
declare(strict_types=1);

namespace App\Tests\App\Service;

use App\Entity\StreamSchedule;
use App\Exception\ConflictingScheduledStreamsException;
use App\Exception\CouldNotModifyStreamScheduleException;
use App\Exception\CouldNotStartLivestreamException;
use App\Exception\ExecutorCouldNotExecuteStreamException;
use App\Repository\StreamScheduleRepository;
use App\Service\StartStreamService;
use App\Service\StopStreamService;
use App\Service\StreamExecutorService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @coversDefaultClass \App\Service\StreamExecutorService
 * @covers ::<!public>
 * @covers ::__construct
 * @uses \App\Entity\StreamSchedule
 * @uses \App\Entity\ScheduleLog
 */
class StreamExecutorServiceTest extends TestCase
{
    /** @var EntityManagerInterface|MockObject */
    private $entityManagerMock;

    /** @var StreamScheduleRepository|MockObject */
    private $streamScheduleRepositoryMock;

    /** @var LoggerInterface|MockObject */
    private $loggerMock;

    /** @var StopStreamService|MockObject */
    private $stopStreamServiceMock;

    /** @var StartStreamService|MockObject */
    private $startStreamServiceMock;

    /** @var StreamExecutorService */
    private $streamExecutorService;

    public function setUp()
    {
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->streamScheduleRepositoryMock = $this->createMock(StreamScheduleRepository::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->stopStreamServiceMock = $this->createMock(StopStreamService::class);
        $this->startStreamServiceMock = $this->createMock(StartStreamService::class);
        $this->streamExecutorService = new StreamExecutorService(
            $this->entityManagerMock,
            $this->streamScheduleRepositoryMock,
            $this->loggerMock,
            $this->stopStreamServiceMock,
            $this->startStreamServiceMock
        );
    }

    /**
     * @throws ConflictingScheduledStreamsException
     * @covers ::getStreamToExecute
     */
    public function testGetStreamToExecuteNothingToExecute()
    {
        $this->streamScheduleRepositoryMock->expects($this->once())
            ->method('findActiveSchedules')
            ->willReturn([]);
        $this->assertNull($this->streamExecutorService->getStreamToExecute());
    }

    /**
     * @throws ConflictingScheduledStreamsException
     * @covers ::getStreamToExecute
     */
    public function testGetStreamToExecuteOneStreamForExecution()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setExecutionTime(new \DateTime('- 1 minutes'));
        $streamSchedule->setOnetimeExecutionDate(new \DateTime('- 1 minutes'));
        $this->streamScheduleRepositoryMock->expects($this->once())
            ->method('findActiveSchedules')
            ->willReturn([$streamSchedule]);

        $streamSchedule = $this->streamExecutorService->getStreamToExecute();
        $this->assertInstanceOf(StreamSchedule::class, $streamSchedule);
    }

    /**
     * @throws ConflictingScheduledStreamsException
     * @covers ::getStreamToExecute
     */
    public function testGetStreamToExecuteConflictingStreams()
    {
        $this->expectException(ConflictingScheduledStreamsException::class);

        $streamSchedule = new StreamSchedule();
        $streamSchedule->setExecutionTime(new \DateTime('- 1 minutes'));
        $streamSchedule->setOnetimeExecutionDate(new \DateTime('- 1 minutes'));
        $this->streamScheduleRepositoryMock->expects($this->once())
            ->method('findActiveSchedules')
            ->willReturn([$streamSchedule, $streamSchedule]);

        $this->entityManagerMock->expects($this->atLeastOnce())->method('persist');
        $this->entityManagerMock->expects($this->once())->method('flush');

        $this->streamExecutorService->getStreamToExecute();
    }

    /**
     * @throws CouldNotModifyStreamScheduleException
     * @throws ExecutorCouldNotExecuteStreamException
     * @covers ::start
     */
    public function testStartSuccess()
    {
        $this->startStreamServiceMock->expects($this->once())->method('process');
        $this->streamScheduleRepositoryMock->expects($this->once())->method('save');
        $this->streamExecutorService->start(new StreamSchedule());
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

        $this->startStreamServiceMock->expects($this->once())
            ->method('process')
            ->willThrowException(CouldNotStartLivestreamException::hostNotAvailable());
        $this->streamScheduleRepositoryMock->expects($this->once())->method('save');

        $this->streamExecutorService->start(new StreamSchedule());
    }

    /**
     * @throws CouldNotModifyStreamScheduleException
     * @throws ExecutorCouldNotExecuteStreamException
     * @covers ::stop
     */
    public function testStopSuccess()
    {
        $this->stopStreamServiceMock->expects($this->once())->method('process');
        $this->streamScheduleRepositoryMock->expects($this->once())->method('save');
        $this->streamExecutorService->stop(new StreamSchedule());
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

        $this->stopStreamServiceMock->expects($this->once())
            ->method('process')
            ->willThrowException(new \InvalidArgumentException());
        $this->streamScheduleRepositoryMock->expects($this->once())->method('save');

        $this->streamExecutorService->stop(new StreamSchedule());
    }
}
