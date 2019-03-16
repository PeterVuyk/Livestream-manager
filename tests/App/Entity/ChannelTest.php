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
     * @covers ::setName
     * @covers ::getName
     */
    public function testName()
    {
        $channel = new Channel();
        $channel->setName('name');
        $this->assertSame('name', $channel->getName());
    }
}
