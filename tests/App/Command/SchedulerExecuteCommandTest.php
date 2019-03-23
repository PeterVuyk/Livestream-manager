<?php
declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\SchedulerExecuteCommand;
use App\Entity\StreamSchedule;
use App\Exception\Messaging\PublishMessageFailedException;
use App\Messaging\Dispatcher\MessagingDispatcher;
use App\Service\StreamScheduleService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @coversDefaultClass \App\Command\SchedulerExecuteCommand
 * @covers ::<!public>
 * @covers ::__construct
 * @uses \App\Entity\StreamSchedule
 * @uses \App\Messaging\Library\Command\StartLivestreamCommand
 * @uses \App\Messaging\Library\Command\StopLivestreamCommand
 */
class SchedulerExecuteCommandTest extends TestCase
{
    /** @var LoggerInterface|MockObject */
    private $loggerMock;

    /** @var StreamScheduleService|MockObject */
    private $streamScheduleServiceMock;

    /** @var MessagingDispatcher|MockObject */
    private $messagingDispatcherMock;

    /** @var CommandTester */
    private $commandTester;

    public function setUp()
    {
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->streamScheduleServiceMock = $this->createMock(StreamScheduleService::class);
        $this->messagingDispatcherMock = $this->createMock(MessagingDispatcher::class);

        $containerMock = $this->createMock(ContainerInterface::class);
        $kernelMock = $this->createMock(KernelInterface::class);
        $kernelMock->expects($this->any())->method('getEnvironment')->willReturn('phpunit');
        $kernelMock->expects($this->any())->method('getBundles')->willReturn([]);
        $kernelMock->expects($this->any())->method('getContainer')->willReturn($containerMock);

        $schedulerExecuteCommand = new SchedulerExecuteCommand(
            $this->loggerMock,
            $this->streamScheduleServiceMock,
            $this->messagingDispatcherMock
        );

        $application = new Application($kernelMock);
        $application->add($schedulerExecuteCommand);

        $schedulerExecuteCommandMock = $application->find(SchedulerExecuteCommand::COMMAND_SCHEDULER_EXECUTE);
        $this->commandTester = new CommandTester($schedulerExecuteCommandMock);
    }

    /**
     * @covers ::execute
     */
    public function testExecuteStartStreamSuccess()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setExecutionTime(new \DateTime('- 1 minutes'));
        $streamSchedule->setOnetimeExecutionDate(new \DateTime('- 1 minutes'));
        $streamSchedule->setChannel('name');
        $this->streamScheduleServiceMock->expects($this->once())
            ->method('getStreamsToExecute')
            ->willReturn([$streamSchedule]);
        $this->messagingDispatcherMock->expects($this->once())->method('sendMessage');
        $this->loggerMock->expects($this->never())->method('error');

        $this->commandTester->execute([SchedulerExecuteCommand::COMMAND_SCHEDULER_EXECUTE]);
    }

    /**
     * @covers ::execute
     */
    public function testExecuteCouldNotSendCommand()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setIsRunning(true);
        $streamSchedule->setStreamDuration(5);
        $streamSchedule->setLastExecution(new \DateTime('- 10 minutes'));
        $streamSchedule->setChannel('name');
        $this->streamScheduleServiceMock->expects($this->once())
            ->method('getStreamsToExecute')
            ->willReturn([$streamSchedule]);
        $this->messagingDispatcherMock->expects($this->once())
            ->method('sendMessage')
            ->willThrowException(PublishMessageFailedException::forMessage('message', ['payload']));
        $this->loggerMock->expects($this->atLeastOnce())->method('error');

        $this->commandTester->execute([SchedulerExecuteCommand::COMMAND_SCHEDULER_EXECUTE]);
    }

    /**
     * @covers ::execute
     */
    public function testExecuteNothingToExecute()
    {
        $this->streamScheduleServiceMock->expects($this->once())
            ->method('getStreamsToExecute')
            ->willReturn([]);
        $this->commandTester->execute([SchedulerExecuteCommand::COMMAND_SCHEDULER_EXECUTE]);
        $this->addToAssertionCount(1);
    }
}
