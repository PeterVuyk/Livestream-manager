<?php
declare(strict_types=1);

namespace App\Tests\App\Controller\Api;

use App\Controller\Api\ApiLivestreamController;
use App\Service\StatusStreamService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @coversDefaultClass \App\Controller\Api\ApiLivestreamController
 * @covers ::<!public>
 * @covers ::__construct
 */
class ApiLivestreamControllerTest extends TestCase
{
    /** @var StatusStreamService|MockObject */
    private $statusStreamServiceMock;

    /** @var LoggerInterface|MockObject */
    private $loggerMock;

    /** @var ApiLivestreamController */
    private $apiLivestreamController;

    public function setUp()
    {
        $this->statusStreamServiceMock = $this->createMock(StatusStreamService::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->apiLivestreamController = new ApiLivestreamController(
            $this->statusStreamServiceMock,
            $this->loggerMock
        );
    }

    /**
     * @covers ::getStatusLivestream
     */
    public function testGetStatusLivestreamSuccess()
    {
        $this->statusStreamServiceMock->expects($this->once())->method('isRunning')->willReturn(false);
        $this->loggerMock->expects($this->never())->method('error');

        $response = $this->apiLivestreamController->getStatusLivestream();
        $this->assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @covers ::getStatusLivestream
     */
    public function testGetStatusLivestreamFailed()
    {
        $this->statusStreamServiceMock->expects($this->once())
            ->method('isRunning')
            ->willThrowException(new \InvalidArgumentException());
        $this->loggerMock->expects($this->once())->method('error');

        $response = $this->apiLivestreamController->getStatusLivestream();
        $this->assertSame(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }


}