<?php
declare(strict_types=1);

namespace App\Tests\Service\StreamProcessing;

use App\Entity\Camera;
use App\Exception\CouldNotModifyCameraException;
use App\Repository\CameraRepository;
use App\Service\StreamProcessing\StreamStateMachine;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Workflow;

/**
 * @coversDefaultClass \App\Service\StreamProcessing\StreamStateMachine
 * @covers ::<!public>
 * @covers ::__construct()
 * @uses \App\Entity\Camera
 * @uses \App\Service\StreamProcessing\StreamStateMachine
 */
class StreamStateMachineTest extends TestCase
{
    /** @var CameraRepository|MockObject */
    private $cameraRepository;

    /** @var Registry|MockObject */
    private $workflows;

    /** @var StreamStateMachine */
    private $streamStateMachine;

    public function setUp()
    {
        $this->cameraRepository = $this->createMock(CameraRepository::class);
        $this->workflows = $this->createMock(Registry::class);
        $this->streamStateMachine = new StreamStateMachine($this->cameraRepository, $this->workflows);
    }

    /**
     * @covers ::can
     */
    public function testCan()
    {
        $workflow = $this->createMock(Workflow::class);
        $workflow->expects($this->once())->method('can')->willReturn(true);
        $this->workflows->expects($this->once())->method('get')->willReturn($workflow);

        $this->assertTrue($this->streamStateMachine->can(new Camera(), 'stop!'));
    }

    /**
     * @throws CouldNotModifyCameraException
     * @covers ::apply
     */
    public function testApplySuccess()
    {
        $workflow = $this->createMock(Workflow::class);
        $workflow->expects($this->once())->method('can')->willReturn(true);
        $this->workflows->expects($this->once())->method('get')->willReturn($workflow);

        $this->cameraRepository->expects($this->once())->method('save');
        $this->streamStateMachine->apply(new Camera(), 'stop!');
        $this->addToAssertionCount(1);
    }

    /**
     * @throws CouldNotModifyCameraException
     * @covers ::apply
     */
    public function testApplyFailed()
    {
        $this->expectException(\InvalidArgumentException::class);

        $workflow = $this->createMock(Workflow::class);
        $workflow->expects($this->once())->method('can')->willReturn(false);
        $this->workflows->expects($this->once())->method('get')->willReturn($workflow);

        $camera = new Camera();
        $camera->setState('some-state');
        $this->cameraRepository->expects($this->never())->method('save');
        $this->streamStateMachine->apply($camera, 'stop!');
    }
}
