<?php
declare(strict_types=1);

namespace App\Tests\App\Controller\Api;

use App\Controller\Api\LivestreamController;
use App\Exception\FailedStartingLivestreamException;
use App\Exception\FailedStoppingLivestreamException;
use App\Service\LivestreamService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class LivestreamControllerTest extends TestCase
{
    /** @var LivestreamController */
    private $livestreamController;

    /** @var MockObject|LivestreamService */
    private $livestreamService;

    public function setUp()
    {
        $livestreamService = $this->livestreamService = $this->createMock(LivestreamService::class);
        $this->livestreamController = new LivestreamController($livestreamService);
    }

    public function testStartLivestreamSuccess()
    {
        $this->livestreamService->expects($this->once())->method('startLivestream');
        $response = $this->livestreamController->startLivestream();
        $this->assertSame(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testStartLivestreamFailed()
    {
        $this->livestreamService
            ->expects($this->once())
            ->method('startLivestream')
            ->willThrowException(FailedStartingLivestreamException::piCamError());

        $response = $this->livestreamController->startLivestream();
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function testStopLivestreamSuccess()
    {
        $this->livestreamService->expects($this->once())->method('stopLivestream');
        $response = $this->livestreamController->stopLivestream();
        $this->assertSame(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testStopLivestreamFailed()
    {
        $this->livestreamService
            ->expects($this->once())
            ->method('stopLivestream')
            ->willThrowException(FailedStoppingLivestreamException::piCamError());

        $response = $this->livestreamController->stopLivestream();
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function testGetStatusLivestream()
    {
        $this->livestreamService->expects($this->once())->method('isLivestreamRunning')->willReturn(true);
        $response = $this->livestreamController->getStatusLivestream();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }
}
