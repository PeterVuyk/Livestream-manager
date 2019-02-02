<?php
declare(strict_types=1);

namespace App\Tests\Messaging\Consumer;

use App\Exception\MessagingQueueConsumerException;
use App\Messaging\Consumer\MessagingConsumer;
use App\Messaging\Library\Command\StartLivestreamCommand;
use App\Messaging\Library\MessageInterface;
use App\Messaging\Serialize\DeserializeInterface;
use Aws\CommandInterface;
use Aws\Exception\AwsException;
use Aws\Result;
use Aws\Sqs\SqsClient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Messaging\Consumer\MessagingConsumer
 * @covers ::<!public>
 * @covers ::__construct()
 */
class MessagingConsumerTest extends TestCase
{
    /** @var SqsClient|MockObject */
    private $sqsClient;

    /** @var DeserializeInterface|MockObject */
    private $deserializer;

    /** @var MessagingConsumer */
    private $messagingConsumer;

    public function setUp()
    {
        $this->sqsClient = $this->createPartialMock(SqsClient::class, ['receiveMessage', 'deleteMessage']);
        $this->deserializer = $this->createMock(DeserializeInterface::class);

        $this->messagingConsumer = new MessagingConsumer($this->sqsClient, $this->deserializer, 'some-url');
    }

    /**
     * @covers ::consume
     * @throws MessagingQueueConsumerException
     */
    public function testConsumeSuccess()
    {
        $resultMock = $this->createMock(Result::class);
        $resultMock->expects($this->atLeastOnce())->method('get')->willReturn([['message', 'ReceiptHandle' => '']]);
        $this->sqsClient->expects($this->once())->method('receiveMessage')->willReturn($resultMock);
        $this->sqsClient->expects($this->once())->method('deleteMessage');

        $this->deserializer->expects($this->once())
            ->method('deserialize')
            ->willReturn(StartLivestreamCommand::create());

        $message = $this->messagingConsumer->consume();
        $this->assertInstanceOf(MessageInterface::class, $message);
    }

    /**
     * @covers ::consume
     * @throws MessagingQueueConsumerException
     */
    public function testConsumeFailedDeleting()
    {
        $this->expectException(MessagingQueueConsumerException::class);

        $resultMock = $this->createMock(Result::class);
        $resultMock->expects($this->atLeastOnce())->method('get')->willReturn([['message', 'ReceiptHandle' => '']]);
        $this->sqsClient->expects($this->once())->method('receiveMessage')->willReturn($resultMock);

        $commandMock = $this->createMock(CommandInterface::class);
        $this->sqsClient->expects($this->once())
            ->method('deleteMessage')
            ->willThrowException(new AwsException('', $commandMock));

        $this->deserializer->expects($this->once())
            ->method('deserialize')
            ->willReturn(StartLivestreamCommand::create());

        $message = $this->messagingConsumer->consume();
        $this->assertInstanceOf(MessageInterface::class, $message);
    }

    /**
     * @covers ::consume
     * @throws MessagingQueueConsumerException
     */
    public function testConsumeFailedReceiving()
    {
        $this->expectException(MessagingQueueConsumerException::class);

        $commandMock = $this->createMock(CommandInterface::class);
        $this->sqsClient->expects($this->once())
            ->method('receiveMessage')
            ->willThrowException(new AwsException('', $commandMock));

        $this->messagingConsumer->consume();
    }
}
