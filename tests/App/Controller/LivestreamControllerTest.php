<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use App\Controller\LivestreamController;
use App\Service\StartStreamService;
use App\Service\StopStreamService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

/**
 * @coversDefaultClass \App\Controller\LivestreamController
 * @covers ::<!public>
 * @covers ::__construct
 * @uses \App\Controller\Controller
 * @uses \App\Service\StartStreamService
 * @uses \App\Service\StopStreamService
 */
class LivestreamControllerTest extends TestCase
{
    /** @var StartStreamService|MockObject */
    private $startStreamServiceMock;

    /** @var StopStreamService|MockObject */
    private $stopStreamServiceMock;

    /** @var RouterInterface|MockObject */
    private $routerMock;

    /** @var LivestreamController */
    private $livestreamController;

    public function setUp()
    {
        $this->startStreamServiceMock = $this->createMock(StartStreamService::class);
        $this->stopStreamServiceMock = $this->createMock(StopStreamService::class);
        $this->routerMock = $this->createMock(RouterInterface::class);
        $this->livestreamController = new LivestreamController(
            $this->startStreamServiceMock,
            $this->stopStreamServiceMock,
            $this->routerMock
        );
    }

    /**
     * @covers ::startStream
     */
    public function testStartStream()
    {
        $this->startStreamServiceMock->expects($this->once())->method('process');
        $this->routerMock->expects($this->once())->method('generate')->willReturn('<p>hi</p>');
        $response = $this->livestreamController->startStream();
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }

    /**
     * @covers ::stopStream
     */
    public function testStopStream()
    {
        $this->stopStreamServiceMock->expects($this->once())->method('process');
        $this->routerMock->expects($this->once())->method('generate')->willReturn('<p>hi</p>');
        $response = $this->livestreamController->stopStream();
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }
}
