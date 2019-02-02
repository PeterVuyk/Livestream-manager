<?php
declare(strict_types=1);

namespace App\Tests\Service\MessageProcessor;

use App\Entity\StreamSchedule;
use App\Exception\ExecutorCouldNotExecuteStreamException;
use App\Repository\StreamScheduleRepository;
use App\Service\MessageProcessor\StopLivestreamProcessor;
use App\Service\StreamProcessing\StopLivestream;
use App\Service\StreamProcessing\StreamScheduleExecutor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @coversDefaultClass \App\Service\MessageProcessor\StopLivestreamProcessor
 * @covers ::<!public>
 * @covers ::__construct
 */
class StopLivestreamProcessorTest extends TestCase
{
    /** @var StreamScheduleRepository|MockObject */
    private $streamScheduleRepository;

    /** @var LoggerInterface|MockObject */
    private $logger;

    /** @var StopLivestream|MockObject */
    private $stopLivestream;

    /** @var StreamScheduleExecutor|MockObject */
    private $streamScheduleExecutor;

    /** @var StopLivestreamProcessor */
    private $stopLivestreamProcessor;

    public function setUp()
    {
        $this->streamScheduleRepository = $this->createMock(StreamScheduleRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->stopLivestream = $this->createMock(StopLivestream::class);
        $this->streamScheduleExecutor = $this->createMock(StreamScheduleExecutor::class);
        $this->stopLivestreamProcessor = new StopLivestreamProcessor(
            $this->streamScheduleRepository,
            $this->logger,
            $this->stopLivestream,
            $this->streamScheduleExecutor
        );
    }

    /**
     * @covers ::process
     */
    public function testProcessStopLivestreamCommandSuccessWithSchedule()
    {
        $this->streamScheduleRepository->expects($this->once())
            ->method('findRunningSchedule')
            ->willReturn(new StreamSchedule());
        $this->streamScheduleExecutor->expects($this->once())->method('stop');
        $this->stopLivestreamProcessor->process();
        $this->addToAssertionCount(1);
    }

    /**
     * @covers ::process
     */
    public function testProcessStopLivestreamCommandSuccess()
    {
        $this->streamScheduleRepository->expects($this->once())->method('findRunningSchedule');
        $this->stopLivestream->expects($this->once())->method('process');
        $this->stopLivestreamProcessor->process();
        $this->addToAssertionCount(1);
    }

    /**
     * @covers ::process
     */
    public function testProcessStopLivestreamCommandFailed()
    {
        $this->streamScheduleRepository->expects($this->once())->method('findRunningSchedule');
        $this->stopLivestream->expects($this->once())
            ->method('process')
            ->willThrowException(ExecutorCouldNotExecuteStreamException::couldNotStopLivestream(new \Exception()));
        $this->stopLivestreamProcessor->process();
        $this->addToAssertionCount(1);
    }
}
