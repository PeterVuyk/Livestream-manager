<?php
declare(strict_types=1);

namespace App\Tests\App\Service;

use App\Exception\Messaging\InvalidMessageTypeException;
use App\Messaging\Library\Event\CameraStateChangedEvent;
use App\Service\ProcessCameraStateChangedEvent;
use App\Service\ProcessMessageDelegator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Service\ProcessMessageDelegator
 * @covers ::<!public>
 * @covers ::__construct()
 * @uses \App\Messaging\Library\Event\CameraStateChangedEvent
 */
class ProcessMessageDelegatorTest extends TestCase
{
    /** @var ProcessCameraStateChangedEvent|MockObject */
    private $processCameraStateChangedEvent;

    /** @var ProcessMessageDelegator */
    private $processMessageDelegator;

    public function setUp()
    {
        $this->processCameraStateChangedEvent = $this->createMock(ProcessCameraStateChangedEvent::class);
        $this->processMessageDelegator = new ProcessMessageDelegator(
            $this->processCameraStateChangedEvent
        );
    }

    /**
     * @covers ::process
     * @throws InvalidMessageTypeException
     */
    public function testProcess()
    {
        $message = CameraStateChangedEvent::create('running', 'inactive', 'hoi');
        $this->processCameraStateChangedEvent->expects($this->once())->method('process')->willReturn($message);

        $this->processMessageDelegator->process($message);
    }
}
