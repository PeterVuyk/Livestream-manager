<?php
declare(strict_types=1);

namespace App\Tests\Service\StreamProcessing;

use App\Entity\StreamSchedule;
use App\Exception\ExecutorCouldNotExecuteStreamException;
use App\Repository\StreamScheduleRepository;
use App\Service\LivestreamService;
use App\Service\StreamProcessing\ProcessLivestreamCommand;
use App\Service\StreamProcessing\StartLivestream;
use App\Service\StreamProcessing\StopLivestream;
use App\Service\StreamProcessing\StreamScheduleExecutor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @coversDefaultClass \App\Service\StreamProcessing\ProcessLivestreamCommand
 * @covers ::<!public>
 * @covers ::__construct()
 */
class ProcessLivestreamCommandTest extends TestCase
{
    /** @var StreamScheduleRepository|MockObject */
    private $streamScheduleRepositoryMock;

    /** @var LoggerInterface|MockObject */
    private $loggerMock;

    /** @var StopLivestream|MockObject */
    private $stopLivestreamMock;

    /** @var StartLivestream|MockObject */
    private $startLivestreamMock;

    /** @var StreamScheduleExecutor|MockObject */
    private $streamScheduleExecutorMock;

    /** @var LivestreamService|MockObject */
    private $livestreamServiceMock;

    /** @var ProcessLivestreamCommand */
    private $processLivestreamCommand;

    public function setUp()
    {
        $this->streamScheduleRepositoryMock = $this->createMock(StreamScheduleRepository::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->stopLivestreamMock = $this->createMock(StopLivestream::class);
        $this->startLivestreamMock = $this->createMock(StartLivestream::class);
        $this->streamScheduleExecutorMock = $this->createMock(StreamScheduleExecutor::class);
        $this->livestreamServiceMock = $this->createMock(LivestreamService::class);
        $this->processLivestreamCommand = new ProcessLivestreamCommand(
            $this->streamScheduleRepositoryMock,
            $this->loggerMock,
            $this->stopLivestreamMock,
            $this->startLivestreamMock,
            $this->streamScheduleExecutorMock,
            $this->livestreamServiceMock
        );
    }

    /**
     * @covers ::processStartLivestreamCommand
     */
    public function testProcessStartLivestreamCommandSuccessWithSchedule()
    {
        $this->livestreamServiceMock->expects($this->once())
            ->method('getStreamToExecute')
            ->willReturn(new StreamSchedule());
        $this->streamScheduleExecutorMock->expects($this->once())->method('start');
        $this->processLivestreamCommand->processStartLivestreamCommand();
        $this->addToAssertionCount(1);
    }

    /**
     * @covers ::processStartLivestreamCommand
     */
    public function testProcessStartLivestreamCommandSuccess()
    {
        $this->livestreamServiceMock->expects($this->once())->method('getStreamToExecute');
        $this->startLivestreamMock->expects($this->once())->method('process');
        $this->processLivestreamCommand->processStartLivestreamCommand();
        $this->addToAssertionCount(1);
    }

    /**
     * @covers ::processStartLivestreamCommand
     */
    public function testProcessStartLivestreamCommandFailed()
    {
        $this->livestreamServiceMock->expects($this->once())->method('getStreamToExecute');
        $this->startLivestreamMock->expects($this->once())
            ->method('process')
            ->willThrowException(ExecutorCouldNotExecuteStreamException::couldNotStartLivestream(new \Exception()));
        $this->processLivestreamCommand->processStartLivestreamCommand();
        $this->addToAssertionCount(1);
    }

    /**
     * @covers ::processStopLivestreamCommand
     */
    public function testProcessStopLivestreamCommandSuccessWithSchedule()
    {
        $this->streamScheduleRepositoryMock->expects($this->once())
            ->method('findRunningSchedule')
            ->willReturn(new StreamSchedule());
        $this->streamScheduleExecutorMock->expects($this->once())->method('stop');
        $this->processLivestreamCommand->processStopLivestreamCommand();
        $this->addToAssertionCount(1);
    }

    /**
     * @covers ::processStopLivestreamCommand
     */
    public function testProcessStopLivestreamCommandSuccess()
    {
        $this->streamScheduleRepositoryMock->expects($this->once())->method('findRunningSchedule');
        $this->stopLivestreamMock->expects($this->once())->method('process');
        $this->processLivestreamCommand->processStopLivestreamCommand();
        $this->addToAssertionCount(1);
    }

    /**
     * @covers ::processStopLivestreamCommand
     */
    public function testProcessStopLivestreamCommandFailed()
    {
        $this->streamScheduleRepositoryMock->expects($this->once())->method('findRunningSchedule');
        $this->stopLivestreamMock->expects($this->once())
            ->method('process')
            ->willThrowException(ExecutorCouldNotExecuteStreamException::couldNotStopLivestream(new \Exception()));
        $this->processLivestreamCommand->processStopLivestreamCommand();
        $this->addToAssertionCount(1);
    }
}
