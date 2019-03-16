<?php
declare(strict_types=1);

namespace App\Tests\Messaging\Library\Command;

use App\Exception\Messaging\UnsupportedMessageException;
use App\Messaging\Library\Command\StartLivestreamCommand;
use App\Messaging\Library\Command\StopLivestreamCommand;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\Assert;

/**
 * @coversDefaultClass \App\Messaging\Library\Command\StartLivestreamCommand
 * @covers ::<!public>
 * @uses \App\Messaging\Library\Command\StartLivestreamCommand
 * @uses \App\Messaging\Library\Command\Command
 */
class StartLivestreamCommandTest extends TestCase
{
    /**
     * @dataProvider startLivestreamCommandProvider
     * @param StartLivestreamCommand $startLivestreamCommand
     * @covers ::getResourceId
     */
    public function testGetResourceId(StartLivestreamCommand $startLivestreamCommand)
    {
        $this->assertNotNull($startLivestreamCommand->getResourceId());
        Assert::uuid($startLivestreamCommand->getResourceId());
    }

    /**
     * @dataProvider startLivestreamCommandProvider
     * @param StartLivestreamCommand $startLivestreamCommand
     * @covers ::getMessageDate
     */
    public function testGetMessageDate(StartLivestreamCommand $startLivestreamCommand)
    {
        $this->assertInstanceOf(\DateTimeInterface::class, $startLivestreamCommand->getMessageDate());
    }

    /**
     * @dataProvider startLivestreamCommandProvider
     * @covers ::getPayload
     * @param StartLivestreamCommand $startLivestreamCommand
     */
    public function testGetPayload(StartLivestreamCommand $startLivestreamCommand)
    {
        $this->assertArrayHasKey(StartLivestreamCommand::RESOURCE_ID_KEY, $startLivestreamCommand->getPayload());
        $this->assertArrayHasKey(StartLivestreamCommand::RESOURCE_ID, $startLivestreamCommand->getPayload());
        $this->assertArrayHasKey(StartLivestreamCommand::MESSAGE_DATE, $startLivestreamCommand->getPayload());
        $this->assertArrayHasKey(StartLivestreamCommand::CHANNEL, $startLivestreamCommand->getPayload());
        $this->assertArrayHasKey(StartLivestreamCommand::USED_MESSAGE_ACTION_KEY, $startLivestreamCommand->getPayload());
    }

    /**
     * @dataProvider startLivestreamCommandProvider
     * @covers ::getResourceIdKey
     * @param StartLivestreamCommand $startLivestreamCommand
     */
    public function testGetResourceIdKey(StartLivestreamCommand $startLivestreamCommand)
    {
        $this->assertSame(StartLivestreamCommand::RESOURCE, $startLivestreamCommand->getResourceIdKey());
    }

    /**
     * @dataProvider startLivestreamCommandProvider
     * @covers ::getResourceIdKey
     * @param StartLivestreamCommand $startLivestreamCommand
     */
    public function testMessageDate(StartLivestreamCommand $startLivestreamCommand)
    {
        $this->assertInstanceOf(\DateTimeInterface::class, $startLivestreamCommand->getMessageDate());
    }

    /**
     * @dataProvider startLivestreamCommandProvider
     * @covers ::getChannel
     * @param StartLivestreamCommand $startLivestreamCommand
     */
    public function testGetChannel(StartLivestreamCommand $startLivestreamCommand)
    {
        $this->assertSame('channel-name', $startLivestreamCommand->getChannel());
    }

    public function startLivestreamCommandProvider(): array
    {
        return [[StartLivestreamCommand::create('channel-name')]];
    }

    /**
     * @throws UnsupportedMessageException
     * @covers ::createFromPayload
     */
    public function testCreateFromPayload()
    {
        $payload = [
            StartLivestreamCommand::RESOURCE_ID => 'some-id',
            StartLivestreamCommand::RESOURCE_ID_KEY => StartLivestreamCommand::RESOURCE,
            StartLivestreamCommand::MESSAGE_DATE => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
            StartLivestreamCommand::CHANNEL => 'channel-name',
            StartLivestreamCommand::USED_MESSAGE_ACTION_KEY => StartLivestreamCommand::USED_MESSAGE_ACTION,
        ];
        $result = StartLivestreamCommand::createFromPayload($payload);

        $this->assertInstanceOf(StartLivestreamCommand::class, $result);
    }
}
