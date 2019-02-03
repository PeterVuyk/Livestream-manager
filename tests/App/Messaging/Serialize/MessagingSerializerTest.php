<?php
declare(strict_types=1);

namespace App\Tests\Messaging\Serialize;

use App\Messaging\Library\Command\StopLivestreamCommand;
use App\Messaging\Serialize\MessagingSerializer;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Messaging\Serialize\MessagingSerializer
 * @covers ::<!public>
 * @uses \App\Messaging\Library\Command\StopLivestreamCommand
 * @uses \App\Messaging\Library\Command\Command
 */
class MessagingSerializerTest extends TestCase
{
    /** @var MessagingSerializer */
    private $messagingSerializer;

    public function setUp()
    {
        $this->messagingSerializer = new MessagingSerializer();
    }

    /**
     * @covers ::serialize
     */
    public function testSerialize()
    {
        $command = $this->getStopLivestreamCommand();
        $serializedMessage = $this->messagingSerializer->serialize($command);

        $deserializedMessage = json_encode($command->getPayload());
        $this->assertEquals($deserializedMessage, $serializedMessage);
    }

    private function getStopLivestreamCommand(): StopLivestreamCommand
    {
        return StopLivestreamCommand::create();
    }
}
