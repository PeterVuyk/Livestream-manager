<?php
declare(strict_types=1);

namespace App\Tests\Messaging\Serialize;

use App\Exception\Messaging\UnsupportedMessageException;
use App\Messaging\Library\Command\StopLivestreamCommand;
use App\Messaging\Serialize\MessagingDeserializer;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Messaging\Serialize\MessagingDeserializer
 * @covers ::<!public>
 * @uses \App\Messaging\Library\Command\StopLivestreamCommand
 */
class MessagingDeserializerTest extends TestCase
{
    /** @var MessagingDeserializer */
    private $messagingDeserializer;

    public function setUp()
    {
        $this->messagingDeserializer = new MessagingDeserializer();
    }

    /**
     * @throws UnsupportedMessageException
     * @covers ::deserialize
     */
    public function testDeserializeSuccess()
    {
        $payload = [
            "methodAction" => "command",
            "resourceId" => "706b3a6e-90ff-4b81-b82e-cded17913620",
            "resourceIdKey" => "stopLivestreamCommand",
            "messageDate" => "2019-02-01 07:49:26"
        ];
        $stopLivestreamCommand = $this->messagingDeserializer->deserialize(
            ['Body' => json_encode(['Message' => json_encode($payload)])]
        );

        $this->assertInstanceOf(StopLivestreamCommand::class, $stopLivestreamCommand);
    }

    /**
     * @throws UnsupportedMessageException
     * @covers ::deserialize
     */
    public function testDeserializeNoExistingCommand()
    {
        $this->expectException(UnsupportedMessageException::class);

        $payload = [
            "methodAction" => "command",
            "resourceId" => "706b3a6e-90ff-4b81-b82e-cded17913620",
            "resourceIdKey" => "some-command-name-that-does-not-exist",
            "messageDate" => "2019-02-01 07:49:26"
        ];
        $this->messagingDeserializer->deserialize(['Body' => json_encode(['Message' => json_encode($payload)])]);
    }
}
