<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use App\Controller\LivestreamController;
use App\Entity\Camera;
use App\Exception\CouldNotModifyCameraException;
use App\Exception\CouldNotStartLivestreamException;
use App\Repository\CameraRepository;
use App\Service\StreamProcessing\StartStreamService;
use App\Service\StreamProcessing\StopStreamService;
use App\Service\StreamProcessing\StreamStateMachine;
use Doctrine\ORM\ORMException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @coversDefaultClass \App\Controller\LivestreamController
 * @covers ::<!public>
 * @covers ::__construct
 * @uses \App\Controller\Controller
 * @uses \App\Service\StreamProcessing\StartStreamService
 * @uses \App\Service\StreamProcessing\StopStreamService
 */
class LivestreamControllerTest extends TestCase
{
    /** @var StartStreamService|MockObject */
    private $startStreamServiceMock;

    /** @var StopStreamService|MockObject */
    private $stopStreamServiceMock;

    /** @var RouterInterface|MockObject */
    private $routerMock;

    /** @var CameraRepository|MockObject */
    private $cameraRepositoryMock;

    /** @var LoggerInterface|MockObject */
    private $loggerMock;

    /** @var FlashBagInterface|MockObject */
    private $flashBagMock;

    /** @var \Twig_Environment|MockObject */
    private $twigMock;

    /** @var StreamStateMachine|MockObject */
    private $streamStateMachineMock;

    /** @var LivestreamController */
    private $livestreamController;

    public function setUp()
    {
        $this->startStreamServiceMock = $this->createMock(StartStreamService::class);
        $this->stopStreamServiceMock = $this->createMock(StopStreamService::class);
        $this->routerMock = $this->createMock(RouterInterface::class);
        $this->cameraRepositoryMock = $this->createMock(CameraRepository::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->flashBagMock = $this->createMock(FlashBagInterface::class);
        $this->twigMock = $this->createMock(\Twig_Environment::class);
        $this->streamStateMachineMock = $this->createMock(StreamStateMachine::class);
        $this->livestreamController = new LivestreamController(
            $this->startStreamServiceMock,
            $this->stopStreamServiceMock,
            $this->routerMock,
            $this->twigMock,
            $this->cameraRepositoryMock,
            $this->loggerMock,
            $this->flashBagMock,
            $this->streamStateMachineMock
        );
    }

    /**
     * @covers ::startStream
     */
    public function testStartStreamSuccess()
    {
        $this->startStreamServiceMock->expects($this->once())->method('process');
        $this->routerMock->expects($this->once())->method('generate')->willReturn('<p>hi</p>');
        $this->loggerMock->expects($this->never())->method('error');
        $response = $this->livestreamController->startStream();
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }


    /**
     * @covers ::startStream
     */
    public function testStartStreamFailed()
    {
        $this->startStreamServiceMock->expects($this->once())
            ->method('process')
            ->willThrowException(CouldNotStartLivestreamException::hostNotAvailable());
        $this->loggerMock->expects($this->atLeastOnce())->method('error');

        $this->routerMock->expects($this->once())->method('generate')->willReturn('<p>hi</p>');
        $response = $this->livestreamController->startStream();
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }

    /**
     * @covers ::stopStream
     */
    public function testStopStreamSuccess()
    {
        $this->stopStreamServiceMock->expects($this->once())->method('process');
        $this->routerMock->expects($this->once())->method('generate')->willReturn('<p>hi</p>');
        $this->loggerMock->expects($this->never())->method('error');
        $response = $this->livestreamController->stopStream();
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }

    /**
     * @covers ::stopStream
     */
    public function testStopStreamFailed()
    {
        $this->stopStreamServiceMock->expects($this->once())
            ->method('process')
            ->willThrowException(CouldNotModifyCameraException::forError(new ORMException()));
        $this->loggerMock->expects($this->atLeastOnce())->method('error');

        $this->routerMock->expects($this->once())->method('generate')->willReturn('<p>hi</p>');
        $response = $this->livestreamController->stopStream();
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }

    /**
     * @covers ::statusStream
     */
    public function testStatusStream()
    {
        $this->twigMock->expects($this->once())->method('render')->willReturn('<p>hi</p>');
        $this->cameraRepositoryMock->expects($this->once())->method('getMainCamera')->willReturn(new Camera());
        $response = $this->livestreamController->statusStream();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @covers ::resetFromFailure
     */
    public function testResetFromFailureSuccess()
    {
        $this->cameraRepositoryMock->expects($this->once())->method('getMainCamera')->willReturn(new Camera());
        $this->streamStateMachineMock->expects($this->once())->method('apply');
        $this->loggerMock->expects($this->never())->method('error');
        $this->routerMock->expects($this->once())->method('generate')->willReturn('<p>hi</p>');

        $response = $this->livestreamController->resetFromFailure();
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }

    /**
     * @covers ::resetFromFailure
     */
    public function testResetFromFailureFailed()
    {
        $this->cameraRepositoryMock->expects($this->once())->method('getMainCamera')->willReturn(new Camera());
        $this->streamStateMachineMock->expects($this->once())
            ->method('apply')
            ->willThrowException(CouldNotModifyCameraException::forError(new ORMException()));
        $this->loggerMock->expects($this->atLeastOnce())->method('error');
        $this->routerMock->expects($this->once())->method('generate')->willReturn('<p>hi</p>');

        $response = $this->livestreamController->resetFromFailure();
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }
}
