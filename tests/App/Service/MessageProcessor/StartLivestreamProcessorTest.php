<?php
declare(strict_types=1);

namespace App\Tests\Service\MessageProcessor;

use App\Entity\StreamSchedule;
use App\Exception\ExecutorCouldNotExecuteStreamException;
use App\Repository\StreamScheduleRepository;
use App\Service\LivestreamService;
use App\Service\MessageProcessor\StartLivestreamProcessor;
use App\Service\StreamProcessing\StartLivestream;
use App\Service\StreamProcessing\StreamScheduleExecutor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @coversDefaultClass \App\Service\MessageProcessor\StartLivestreamProcessor
 * @covers ::<!public>
 * @covers ::__construct
 */
class StartLivestreamProcessorTest extends TestCase
{
    /** @var StreamScheduleRepository|MockObject */
    private $streamScheduleRepository;

    /** @var LoggerInterface|MockObject */
    private $logger;

    /** @var StartLivestream|MockObject */
    private $startLivestream;

    /** @var StreamScheduleExecutor|MockObject */
    private $streamScheduleExecutor;

    /** @var LivestreamService|MockObject */
    private $livestreamService;

    /** @var StartLivestreamProcessor */
    private $startLivestreamProcessor;

    public function setUp()
    {
        $this->streamScheduleRepository = $this->createMock(StreamScheduleRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->startLivestream = $this->createMock(StartLivestream::class);
        $this->streamScheduleExecutor = $this->createMock(StreamScheduleExecutor::class);
        $this->livestreamService = $this->createMock(LivestreamService::class);
        $this->startLivestreamProcessor = new StartLivestreamProcessor(
            $this->streamScheduleRepository,
            $this->logger,
            $this->startLivestream,
            $this->streamScheduleExecutor,
            $this->livestreamService
        );
    }

    /**
     * @covers ::process
     */
    public function testProcessStartLivestreamCommandSuccessWithSchedule()
    {
        $this->livestreamService->expects($this->once())
            ->method('getStreamToExecute')
            ->willReturn(new StreamSchedule());
        $this->streamScheduleExecutor->expects($this->once())->method('start');
        $this->startLivestreamProcessor->process();
        $this->addToAssertionCount(1);
    }

    /**
     * @covers ::process
     */
    public function testProcessStartLivestreamCommandSuccess()
    {
        $this->livestreamService->expects($this->once())->method('getStreamToExecute');
        $this->startLivestream->expects($this->once())->method('process');
        $this->startLivestreamProcessor->process();
        $this->addToAssertionCount(1);
    }

    /**
     * @covers ::process
     */
    public function testProcessStartLivestreamCommandFailed()
    {
        $this->livestreamService->expects($this->once())->method('getStreamToExecute');
        $this->startLivestream->expects($this->once())
            ->method('process')
            ->willThrowException(ExecutorCouldNotExecuteStreamException::couldNotStartLivestream(new \Exception()));
        $this->startLivestreamProcessor->process();
        $this->addToAssertionCount(1);
    }
}
