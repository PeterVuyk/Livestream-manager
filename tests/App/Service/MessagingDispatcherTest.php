<?php
declare(strict_types=1);

namespace App\Tests\App\Service;

use App\Exception\PublishMessageFailedException;
use App\Service\MessagingDispatcher;
use Aws\Sns\SnsClient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Service\MessagingDispatcher
 * @covers ::<!public>
 * @covers ::__construct()
 */
class MessagingDispatcherTest extends TestCase
{
    /** @var SnsClient|MockObject */
    private $snsClientMock;

    /** @var MessagingDispatcher */
    private $messagingDispatcher;

    public function setUp()
    {
        $this->snsClientMock = $this->getMockBuilder(SnsClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['publish'])
            ->getMock();
        $this->messagingDispatcher = new MessagingDispatcher($this->snsClientMock);
    }

    /**
     * @throws PublishMessageFailedException
     * @covers ::sendMessage
     */
    public function testSendMessageSuccess()
    {
        $this->snsClientMock->expects($this->once())->method('publish');
        $this->messagingDispatcher->sendMessage('some-topic-arn', 'message');
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
        $this->messagingDispatcher->sendMessage('some-topic-arn', 'message');
    }
}
