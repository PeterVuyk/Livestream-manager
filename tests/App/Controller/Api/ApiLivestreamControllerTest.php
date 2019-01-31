<?php
declare(strict_types=1);

namespace App\Tests\App\Controller\Api;

use App\Controller\Api\ApiLivestreamController;
use App\Entity\Camera;
use App\Exception\CouldNotFindMainCameraException;
use App\Service\LivestreamService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @coversDefaultClass \App\Controller\Api\ApiLivestreamController
 * @covers ::<!public>
 * @covers ::__construct
 * @uses \App\Entity\Camera
 */
class ApiLivestreamControllerTest extends TestCase
{
    /** @var LivestreamService|MockObject */
    private $livestreamServiceMock;

    /** @var LoggerInterface|MockObject */
    private $loggerMock;

    /** @var ApiLivestreamController */
    private $apiLivestreamController;

    public function setUp()
    {
        $this->livestreamServiceMock = $this->createMock(LivestreamService::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->apiLivestreamController = new ApiLivestreamController(
            $this->livestreamServiceMock,
            $this->loggerMock
        );
    }

    /**
     * @covers ::getStatusLivestream
     */
    public function testGetStatusLivestreamSuccess()
    {
        $camera = new Camera();
        $camera->setState('some-state');
        $this->livestreamServiceMock->expects($this->once())->method('getMainCameraStatus')->willReturn($camera);
        $this->loggerMock->expects($this->never())->method('error');

        $response = $this->apiLivestreamController->getStatusLivestream();
        $this->assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @covers ::getStatusLivestream
     */
    public function testGetStatusLivestreamFailed()
    {
        $this->livestreamServiceMock->expects($this->once())
            ->method('getMainCameraStatus')
            ->willThrowException(new CouldNotFindMainCameraException());
        $this->loggerMock->expects($this->once())->method('error');

        $response = $this->apiLivestreamController->getStatusLivestream();
        $this->assertSame(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }


}
