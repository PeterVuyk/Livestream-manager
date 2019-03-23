<?php
declare(strict_types=1);

namespace App\Tests\App\Entity;

use App\Entity\Channel;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Entity\Channel
 * @covers ::<!public>
 */
class ChannelTest extends TestCase
{
    /**
     * @covers ::setChannelName
     * @covers ::getChannelName
     */
    public function testName()
    {
        $channel = new Channel();
        $channel->setChannelName('name');
        $this->assertSame('name', $channel->getChannelName());
    }

    /**
     * @covers ::getUserName
     * @covers ::setUserName
     */
    public function testUserName()
    {
        $channel = new Channel();
        $channel->setUsername('userName');
        $this->assertSame('userName', $channel->getUsername());
    }

    /**
     * @covers ::getHost
     * @covers ::setHost
     */
    public function testHost()
    {
        $channel = new Channel();
        $channel->setHost('userName');
        $this->assertSame('userName', $channel->getHost());
    }

    /**
     * @covers ::setSecret
     * @covers ::getSecret
     */
    public function testSecret()
    {
        $channel = new Channel();
        $channel->setSecret('very-secret-password');
        $this->assertSame('very-secret-password', $channel->getSecret());
    }
}
