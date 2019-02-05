<?php
declare(strict_types=1);

namespace App\Tests\Messaging\Library\Command;

use App\Exception\Messaging\UnsupportedMessageException;
use App\Messaging\Library\Command\StopLivestreamCommand;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\Assert;

/**
 * @coversDefaultClass \App\Messaging\Library\Command\StopLivestreamCommand
 * @covers ::<!public>
 * @uses \App\Messaging\Library\Command\StopLivestreamCommand
 * @uses \App\Messaging\Library\Command\Command
 */
class StopLivestreamCommandTest extends TestCase
{
    /**
     * @dataProvider stopLivestreamCommandProvider
     * @param StopLivestreamCommand $stopLivestreamCommand
     * @covers ::getResourceId
     */
    public function testGetResourceId(StopLivestreamCommand $stopLivestreamCommand)
    {
        $this->assertNotNull($stopLivestreamCommand->getResourceId());
        Assert::uuid($stopLivestreamCommand->getResourceId());
    }

    /**
     * @dataProvider stopLivestreamCommandProvider
     * @param StopLivestreamCommand $stopLivestreamCommand
     * @covers ::getMessageDate
     */
    public function testGetMessageDate(StopLivestreamCommand $stopLivestreamCommand)
    {
        $this->assertInstanceOf(\DateTimeInterface::class, $stopLivestreamCommand->getMessageDate());
    }

    /**
     * @dataProvider stopLivestreamCommandProvider
     * @covers ::getPayload
     * @param StopLivestreamCommand $stopLivestreamCommand
     */
    public function testGetPayload(StopLivestreamCommand $stopLivestreamCommand)
    {
        $this->assertArrayHasKey(StopLivestreamCommand::RESOURCE_ID_KEY, $stopLivestreamCommand->getPayload());
        $this->assertArrayHasKey(StopLivestreamCommand::RESOURCE_ID, $stopLivestreamCommand->getPayload());
        $this->assertArrayHasKey(StopLivestreamCommand::MESSAGE_DATE, $stopLivestreamCommand->getPayload());
        $this->assertArrayHasKey(StopLivestreamCommand::USED_MESSAGE_ACTION_KEY, $stopLivestreamCommand->getPayload());
    }

    /**
     * @dataProvider stopLivestreamCommandProvider
     * @covers ::getResourceIdKey
     * @param StopLivestreamCommand $stopLivestreamCommand
     */
    public function testGetResourceIdKey(StopLivestreamCommand $stopLivestreamCommand)
    {
        $this->assertSame(StopLivestreamCommand::RESOURCE, $stopLivestreamCommand->getResourceIdKey());
    }

    /**
     * @dataProvider stopLivestreamCommandProvider
     * @covers ::getResourceIdKey
     * @param StopLivestreamCommand $stopLivestreamCommand
     */
    public function testDate(StopLivestreamCommand $stopLivestreamCommand)
    {
        $this->assertInstanceOf(\DateTimeInterface::class, $stopLivestreamCommand->getMessageDate());
    }

    public function stopLivestreamCommandProvider(): array
    {
        return [[StopLivestreamCommand::create()]];
    }

    /**
     * @covers ::createFromPayload
     * @throws UnsupportedMessageException
     */
    public function testCreateFromPayload()
    {
        $payload = [
            StopLivestreamCommand::RESOURCE_ID => 'some-id',
            StopLivestreamCommand::RESOURCE_ID_KEY => StopLivestreamCommand::RESOURCE,
            StopLivestreamCommand::MESSAGE_DATE => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
            StopLivestreamCommand::USED_MESSAGE_ACTION_KEY => StopLivestreamCommand::USED_MESSAGE_ACTION,
        ];
        $result = StopLivestreamCommand::createFromPayload($payload);

        $this->assertInstanceOf(StopLivestreamCommand::class, $result);
    }
}
