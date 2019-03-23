<?php
declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\StopLivestreamCommand;
use App\Entity\Camera;
use App\Exception\Messaging\PublishMessageFailedException;
use App\Messaging\Dispatcher\MessagingDispatcher;
use App\Service\LivestreamService;
use App\Service\StreamProcessing\StreamStateMachine;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @coversDefaultClass \App\Command\StopLivestreamCommand
 * @covers ::<!public>
 * @covers ::__construct
 * @uses \App\Messaging\Library\Command\StopLivestreamCommand
 */
class StopLivestreamCommandTest extends TestCase
{
    /** @var MessagingDispatcher|MockObject */
    private $messagingDispatcher;

    /** @var LoggerInterface|MockObject */
    private $logger;

    /** @var CommandTester */
    private $commandTester;

    public function setUp()
    {
        $this->messagingDispatcher = $this->createMock(MessagingDispatcher::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $containerMock = $this->createMock(ContainerInterface::class);
        $kernelMock = $this->createMock(KernelInterface::class);
        $kernelMock->expects($this->any())->method('getEnvironment')->willReturn('phpunit');
        $kernelMock->expects($this->any())->method('getBundles')->willReturn([]);
        $kernelMock->expects($this->any())->method('getContainer')->willReturn($containerMock);

        $stopLivestreamCommand = new StopLivestreamCommand(
            $this->messagingDispatcher,
            $this->logger
        );

        $application = new Application($kernelMock);
        $application->add($stopLivestreamCommand);

        $stopLivestreamCommandMock = $application->find(StopLivestreamCommand::COMMAND_STOP_LIVESTREAM);
        $this->commandTester = new CommandTester($stopLivestreamCommandMock);
    }

    /**
     * @covers ::execute
     */
    public function testExecuteSuccess()
    {
        $this->messagingDispatcher->expects($this->once())->method('sendMessage');
        $this->logger->expects($this->never())->method('error');

        $this->commandTester->execute(
            ['command' => StopLivestreamCommand::COMMAND_STOP_LIVESTREAM, 'channelName' => 'channelName']
        );
    }

    /**
     * @covers ::execute
     */
    public function testExecuteDispatchFailed()
    {
        $this->messagingDispatcher->expects($this->once())
            ->method('sendMessage')
            ->willThrowException(PublishMessageFailedException::forMessage('topic', []));

        $this->logger->expects($this->atLeastOnce())->method('error');

        $this->commandTester->execute(
            ['command' => StopLivestreamCommand::COMMAND_STOP_LIVESTREAM, 'channelName' => 'channelName']
        );
    }
}
