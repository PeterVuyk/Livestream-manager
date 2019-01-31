<?php
declare(strict_types=1);

namespace App\Tests\App\Service;

use App\Entity\Camera;
use App\Exception\CouldNotFindMainCameraException;
use App\Repository\CameraRepository;
use App\Service\LivestreamService;
use App\Service\StateMachineInterface;
use App\Service\StreamProcessing\StreamStateMachine;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Service\LivestreamService
 * @covers ::<!public>
 * @covers ::__construct()
 * @uses \App\Entity\Camera
 */
class LivestreamServiceTest extends TestCase
{
    /** @var StateMachineInterface|MockObject */
    private $streamStateMachineMock;

    /** @var CameraRepository|MockObject */
    private $cameraRepositoryMock;

    /** @var LivestreamService */
    private $livestreamService;

    public function setUp()
    {
        $this->streamStateMachineMock = $this->createMock(StreamStateMachine::class);
        $this->cameraRepositoryMock = $this->createMock(CameraRepository::class);
        $this->livestreamService = new LivestreamService($this->streamStateMachineMock, $this->cameraRepositoryMock);
    }

    /**
     * @throws CouldNotFindMainCameraException
     * @covers ::getMainCameraStatus
     */
    public function testGetMainCameraStatusSuccess()
    {
        $this->cameraRepositoryMock->expects($this->once())->method('getMainCamera')->willReturn(new Camera());

        $camera = $this->livestreamService->getMainCameraStatus();
        $this->assertInstanceOf(Camera::class, $camera);
    }

    /**
     * @throws CouldNotFindMainCameraException
     * @covers ::getMainCameraStatus
     */
    public function testGetMainCameraStatusFailed()
    {
        $this->expectException(CouldNotFindMainCameraException::class);
        $this->cameraRepositoryMock->expects($this->once())->method('getMainCamera');

        $this->livestreamService->getMainCameraStatus();
    }
}
