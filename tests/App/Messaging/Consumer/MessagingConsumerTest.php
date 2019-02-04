<?php
declare(strict_types=1);

namespace App\Tests\Messaging\Consumer;

use App\Exception\Messaging\MessagingQueueConsumerException;
use App\Messaging\Consumer\MessagingConsumer;
use App\Messaging\Library\MessageInterface;
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
 * @uses \App\Messaging\Library\Command\StartLivestreamCommand
 */
class MessagingConsumerTest extends TestCase
{
    /** @var SqsClient|MockObject */
    private $sqsClient;

    /** @var MessagingConsumer */
    private $messagingConsumer;

    public function setUp()
    {
        $this->sqsClient = $this->createPartialMock(SqsClient::class, ['receiveMessage', 'deleteMessage']);
        $this->messagingConsumer = new MessagingConsumer($this->sqsClient, 'some-url');
    }

    /**
     * @covers ::consume
     * @throws MessagingQueueConsumerException
     */
    public function testConsumeSuccess()
    {
        $resultMock = $this->createMock(Result::class);
        $resultMock->expects($this->any())->method('get')->willReturn([['Messages' => 1]]);
        $this->sqsClient->expects($this->once())->method('receiveMessage')->willReturn($resultMock);

        $message = $this->messagingConsumer->consume();
        $this->assertArrayHasKey('Messages', $message);
    }

    /**
     * @covers ::consume
     * @throws MessagingQueueConsumerException
     */
    public function testConsumeNoResult()
    {
        $resultMock = $this->createMock(Result::class);
        $resultMock->expects($this->any())->method('get')->willReturn(null);
        $this->sqsClient->expects($this->once())->method('receiveMessage')->willReturn($resultMock);

        $message = $this->messagingConsumer->consume();
        $this->assertSame([], $message);
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

    /**
     * @covers ::delete
     * @throws MessagingQueueConsumerException
     */
    public function testDeleteSuccess()
    {
        $this->createMock(CommandInterface::class);
        $this->sqsClient->expects($this->once())
            ->method('deleteMessage');

        $this->messagingConsumer->delete(['message' => '', 'ReceiptHandle' => '']);
        $this->addToAssertionCount(1);
    }

    /**
     * @covers ::delete
     * @throws MessagingQueueConsumerException
     */
    public function testDeleteFailed()
    {
        $this->expectException(MessagingQueueConsumerException::class);
        $commandMock = $this->createMock(CommandInterface::class);
        $this->sqsClient->expects($this->once())
            ->method('deleteMessage')
            ->willThrowException(new AwsException('', $commandMock));

        $this->messagingConsumer->delete(['message' => '', 'ReceiptHandle' => '']);
    }
}
