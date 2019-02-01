<?php
declare(strict_types=1);

namespace App\Tests\Messaging\Dispatcher;

use App\Exception\PublishMessageFailedException;
use App\Messaging\Dispatcher\MessagingDispatcher;
use App\Messaging\Library\Command\StopLivestreamCommand;
use App\Messaging\Serialize\SerializeInterface;
use Aws\Sns\SnsClient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Messaging\Dispatcher\MessagingDispatcher
 * @covers ::<!public>
 * @covers ::__construct()
 * @uses \App\Messaging\Library\Command\StopLivestreamCommand
 * @uses \App\Messaging\Library\Command\Command
 */
class MessagingDispatcherTest extends TestCase
{
    /** @var SnsClient|MockObject */
    private $snsClientMock;

    /** @var SerializeInterface */
    private $serialize;

    /** @var MessagingDispatcher */
    private $messagingDispatcher;

    public function setUp()
    {
        $this->serialize = $this->createMock(SerializeInterface::class);
        $this->snsClientMock = $this->getMockBuilder(SnsClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['publish'])
            ->getMock();
        $this->messagingDispatcher = new MessagingDispatcher($this->snsClientMock, $this->serialize);
    }

    /**
     * @throws PublishMessageFailedException
     * @covers ::sendMessage
     */
    public function testSendMessageSuccess()
    {
        $this->snsClientMock->expects($this->once())->method('publish');
        $this->messagingDispatcher->sendMessage('some-topic-arn', $this->getStopLivestreamCommand());
        $this->addToAssertionCount(1);
    }

    /**
     * @throws PublishMessageFailedException
     * @covers ::sendMessage
     */
    public function testSendMessageFailed()
    {
        $this->expectException(PublishMessageFailedException::class);
        $this->snsClientMock->expects($this->once())->method('publish')->willThrowException(new \Exception());
        $this->messagingDispatcher->sendMessage('some-topic-arn', $this->getStopLivestreamCommand());
    }

    private function getStopLivestreamCommand(): StopLivestreamCommand
    {
        return StopLivestreamCommand::create();
    }
}
