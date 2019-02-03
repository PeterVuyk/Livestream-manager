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
     * @covers ::getCommandDate
     */
    public function testGetCommandDate(StartLivestreamCommand $startLivestreamCommand)
    {
        $this->assertInstanceOf(\DateTimeInterface::class, $startLivestreamCommand->getCommandDate());
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
        $this->assertArrayHasKey(StartLivestreamCommand::COMMAND_DATE, $startLivestreamCommand->getPayload());
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
    public function testDate(StartLivestreamCommand $startLivestreamCommand)
    {
        $this->assertInstanceOf(\DateTimeInterface::class, $startLivestreamCommand->getCommandDate());
    }

    public function startLivestreamCommandProvider(): array
    {
        return [[StartLivestreamCommand::create()]];
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
            StopLivestreamCommand::COMMAND_DATE => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
            StartLivestreamCommand::USED_MESSAGE_ACTION_KEY => StartLivestreamCommand::USED_MESSAGE_ACTION,
        ];
        $result = StartLivestreamCommand::createFromPayload($payload);

        $this->assertInstanceOf(StartLivestreamCommand::class, $result);
    }
}
