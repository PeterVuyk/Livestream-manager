<?php
declare(strict_types=1);

namespace App\Tests\App\Service;

use App\Entity\Channel;
use App\Exception\Repository\CouldNotModifyChannelException;
use App\Repository\ChannelRepository;
use App\Service\ChannelService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Service\ChannelService
 * @covers ::<!public>
 * @covers ::__construct()
 * @uses \App\Entity\Channel
 * @uses \App\Service\ChannelService
 */
class ChannelServiceTest extends TestCase
{
    /** @var ChannelRepository|MockObject */
    private $channelRepository;

    /** @var ChannelService */
    private $channelService;

    public function setUp()
    {
        $this->channelRepository = $this->createMock(ChannelRepository::class);
        $this->channelService = new ChannelService($this->channelRepository);
    }

    /**
     * @covers ::getChannelByName
     */
    public function testGetChannelByName()
    {
        $this->channelRepository->expects($this->once())->method('findOneBy')->willReturn(new Channel());
        $channel = $this->channelService->getChannelByName('channelName');
        $this->assertInstanceOf(Channel::class, $channel);
    }

    /**
     * @throws CouldNotModifyChannelException
     * @covers ::createChannel
     */
    public function createChannel()
    {
        $this->channelRepository->expects($this->once())->method('save');
        $this->channelService->createChannel(new Channel());
    }

    /**
     * @throws CouldNotModifyChannelException
     * @covers ::removeChannelByName
     */
    public function testRemoveChannelByName()
    {
        $this->channelRepository->expects($this->once())->method('findOneBy')->willReturn(new Channel());
        $this->channelRepository->expects($this->once())->method('remove');
        $this->channelService->removeChannelByName('channel');
    }

    /**
     * @throws CouldNotModifyChannelException
     * @covers ::updateChannel
     */
    public function testUpdateChannel()
    {
        $this->channelRepository->expects($this->once())->method('save');
        $this->channelService->updateChannel(new Channel());
        $this->addToAssertionCount(1);
    }
}
