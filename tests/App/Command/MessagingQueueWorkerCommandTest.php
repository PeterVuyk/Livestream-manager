<?php
declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\MessagingQueueWorkerCommand;
use App\Messaging\Consumer\MessagingQueueWorker;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @coversDefaultClass \App\Command\MessagingQueueWorkerCommand
 * @covers ::<!public>
 * @covers ::__construct
 */
class MessagingQueueWorkerCommandTest extends TestCase
{
    /** @var MessagingQueueWorker|MockObject */
    private $messagingQueueWorker;

    /** @var LoggerInterface|MockObject */
    private $logger;

    /** @var CommandTester */
    private $commandTester;

    public function setUp()
    {
        $this->messagingQueueWorker = $this->createMock(MessagingQueueWorker::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $containerMock = $this->createMock(ContainerInterface::class);
        $kernelMock = $this->createMock(KernelInterface::class);
        $kernelMock->expects($this->any())->method('getEnvironment')->willReturn('phpunit');
        $kernelMock->expects($this->any())->method('getBundles')->willReturn([]);
        $kernelMock->expects($this->any())->method('getContainer')->willReturn($containerMock);

        $schedulerExecuteCommand = new MessagingQueueWorkerCommand(
            $this->messagingQueueWorker,
            $this->logger
        );

        $application = new Application($kernelMock);
        $application->add($schedulerExecuteCommand);

        $schedulerExecuteCommandMock = $application->find(MessagingQueueWorkerCommand::COMMAND_MESSAGING_WORKER);
        $this->commandTester = new CommandTester($schedulerExecuteCommandMock);
    }

    /**
     * @covers ::execute
     */
    public function testExecute()
    {
        $this->messagingQueueWorker->expects($this->once())->method('__invoke');
        $this->logger->expects($this->once())->method('error');
        $this->commandTester->execute([MessagingQueueWorkerCommand::COMMAND_MESSAGING_WORKER]);
        $this->addToAssertionCount(1);
    }
}
