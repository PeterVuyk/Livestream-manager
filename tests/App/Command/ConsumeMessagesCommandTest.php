<?php
declare(strict_types=1);

namespace App\Tests\App\Command;

use App\Command\ConsumeMessagesCommand;
use App\Exception\MessagingQueueConsumerException;
use App\Messaging\Consumer\MessagingConsumer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @coversDefaultClass \App\Command\ConsumeMessagesCommand
 * @covers ::<!public>
 * @covers ::__construct
 */
class ConsumeMessagesCommandTest extends TestCase
{
    /** @var MessagingConsumer|MockObject */
    private $messagingConsumerMock;

    /** @var LoggerInterface|MockObject */
    private $loggerMock;

    /** @var CommandTester */
    private $commandTester;

    public function setUp()
    {
        $this->messagingConsumerMock = $this->createMock(MessagingConsumer::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $containerMock = $this->createMock(ContainerInterface::class);
        $kernelMock = $this->createMock(KernelInterface::class);
        $kernelMock->expects($this->any())->method('getEnvironment')->willReturn('phpunit');
        $kernelMock->expects($this->any())->method('getBundles')->willReturn([]);
        $kernelMock->expects($this->any())->method('getContainer')->willReturn($containerMock);

        $schedulerExecuteCommand = new ConsumeMessagesCommand(
            $this->messagingConsumerMock,
            $this->loggerMock
        );

        $application = new Application($kernelMock);
        $application->add($schedulerExecuteCommand);

        $schedulerExecuteCommandMock = $application->find(ConsumeMessagesCommand::COMMAND_STREAM_MESSAGES);
        $this->commandTester = new CommandTester($schedulerExecuteCommandMock);
    }

    /**
     * @covers ::execute
     */
    public function testExecuteSuccess()
    {
        $this->messagingConsumerMock->expects($this->once())->method('consume');
        $this->loggerMock->expects($this->never())->method('error');

        $this->commandTester->execute([ConsumeMessagesCommand::COMMAND_STREAM_MESSAGES]);
        $this->addToAssertionCount(1);
    }

    /**
     * @covers ::execute
     */
    public function testExecuteFailed()
    {
        $this->messagingConsumerMock->expects($this->once())
            ->method('consume')
            ->willThrowException(MessagingQueueConsumerException::fromError(new \Exception()));
        $this->loggerMock->expects($this->atLeastOnce())->method('error');

        $this->commandTester->execute([ConsumeMessagesCommand::COMMAND_STREAM_MESSAGES]);
        $this->addToAssertionCount(1);
    }
}
