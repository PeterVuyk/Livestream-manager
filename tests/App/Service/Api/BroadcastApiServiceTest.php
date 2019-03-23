<?php
declare(strict_types=1);

namespace App\Tests\App\Service\Api;

use App\Entity\Channel;
use App\Exception\Livestream\CouldNotApiCallBroadcastException;
use App\Repository\ChannelRepository;
use App\Service\Api\BroadcastApiService;
use GuzzleHttp\Client;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @coversDefaultClass \App\Service\Api\BroadcastApiService
 * @covers ::<!public>
 * @covers ::__construct()
 * @uses \App\Entity\Channel
 */
class BroadcastApiServiceTest extends TestCase
{
    /** @var ChannelRepository|MockObject */
    private $channelRepository;

    /** @var Client|MockObject */
    private $client;

    /** @var BroadcastApiService */
    private $broadcastApiService;

    public function setUp()
    {
        $this->channelRepository = $this->createMock(ChannelRepository::class);
        $this->client = $this->createMock(Client::class);
        $this->broadcastApiService = new BroadcastApiService($this->channelRepository, $this->client);
    }

    /**
     * @covers ::getStatusLivestream
     * @throws CouldNotApiCallBroadcastException
     */
    public function testGetStatusLivestreamSuccess()
    {
        $this->channelRepository->expects($this->once())->method('findOneBy')->willReturn(new Channel());
        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())->method('getStatusCode')->willReturn(200);
        $streamInterface = $this->createMock(StreamInterface::class);
        $streamInterface->expects($this->once())->method('getContents')->willReturn('{"status": "inactive"}');
        $response->expects($this->once())->method('getBody')->willReturn($streamInterface);
        $this->client->expects($this->once())->method('request')->willReturn($response);

        $this->assertSame('inactive', $this->broadcastApiService->getStatusLivestream('channel'));
    }

    /**
     * @covers ::getStatusLivestream
     * @throws CouldNotApiCallBroadcastException
     */
    public function testGetStatusLivestreamFailed()
    {
        $this->expectException(CouldNotApiCallBroadcastException::class);

        $this->channelRepository->expects($this->once())->method('findOneBy')->willReturn(new Channel());
        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->atLeastOnce())->method('getStatusCode')->willReturn(400);
        $streamInterface = $this->createMock(StreamInterface::class);
        $streamInterface->expects($this->atLeastOnce())->method('getContents')->willReturn('');
        $response->expects($this->atLeastOnce())->method('getBody')->willReturn($streamInterface);
        $this->client->expects($this->once())->method('request')->willReturn($response);

        $this->broadcastApiService->getStatusLivestream('channel');
    }

    /**
     * @covers ::resetFromFailure
     * @throws CouldNotApiCallBroadcastException
     */
    public function testResetFromFailureSuccess()
    {
        $this->channelRepository->expects($this->once())->method('findOneBy')->willReturn(new Channel());
        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->atLeastOnce())->method('getStatusCode')->willReturn(201);
        $this->client->expects($this->once())->method('request')->willReturn($response);

        $this->broadcastApiService->resetFromFailure('channel');
        $this->addToAssertionCount(1);
    }

    /**
     * @covers ::resetFromFailure
     * @throws CouldNotApiCallBroadcastException
     */
    public function testResetFromFailureFailed()
    {
        $this->expectException(CouldNotApiCallBroadcastException::class);

        $this->channelRepository->expects($this->once())->method('findOneBy')->willReturn(new Channel());
        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->atLeastOnce())->method('getStatusCode')->willReturn(401);
        $streamInterface = $this->createMock(StreamInterface::class);
        $streamInterface->expects($this->atLeastOnce())->method('getContents')->willReturn('');
        $response->expects($this->atLeastOnce())->method('getBody')->willReturn($streamInterface);
        $this->client->expects($this->once())->method('request')->willReturn($response);

        $this->broadcastApiService->resetFromFailure('channel');
    }
}
