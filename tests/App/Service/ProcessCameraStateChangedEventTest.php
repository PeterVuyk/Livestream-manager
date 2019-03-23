<?php
declare(strict_types=1);

namespace App\Tests\App\Service;

use App\Entity\StreamSchedule;
use App\Messaging\Library\Event\CameraStateChangedEvent;
use App\Repository\StreamScheduleRepository;
use App\Service\ProcessCameraStateChangedEvent;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @coversDefaultClass \App\Service\ProcessCameraStateChangedEvent
 * @covers ::<!public>
 * @covers ::__construct()
 * @uses \App\Entity\StreamSchedule
 * @uses \App\Entity\ScheduleLog
 * @uses \App\Messaging\Library\Event\CameraStateChangedEvent
 */
class ProcessCameraStateChangedEventTest extends TestCase
{
    /** @var StreamScheduleRepository|MockObject */
    private $streamScheduleRepository;

    /** @var LoggerInterface|MockObject */
    private $logger;

    /** @var ProcessCameraStateChangedEvent */
    private $processCameraStateChangedEvent;

    public function setUp()
    {
        $this->streamScheduleRepository = $this->createMock(StreamScheduleRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->processCameraStateChangedEvent = new ProcessCameraStateChangedEvent(
            $this->streamScheduleRepository,
            $this->logger
        );
    }

    /**
     * @covers ::process
     */
    public function testProcess()
    {
        $this->streamScheduleRepository->expects($this->once())
            ->method('getStreamToExecuteByChannelName')
            ->willReturn(new StreamSchedule());
        $this->streamScheduleRepository->expects($this->once())->method('save');

        $message = CameraStateChangedEvent::create('running', 'inactive', 'hoi');

        $this->processCameraStateChangedEvent->process($message);
        $this->addToAssertionCount(1);
    }
}
