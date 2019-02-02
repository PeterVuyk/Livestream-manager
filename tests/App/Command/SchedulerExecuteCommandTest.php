<?php
declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\SchedulerExecuteCommand;
use App\Entity\StreamSchedule;
use App\Exception\ConflictingScheduledStreamsException;
use App\Exception\PublishMessageFailedException;
use App\Service\LivestreamService;
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
 */
class SchedulerExecuteCommandTest extends TestCase
{
    /** @var LivestreamService|MockObject */
    private $livestreamServiceMock;

    /** @var LoggerInterface|MockObject */
    private $loggerMock;

    /** @var CommandTester */
    private $commandTester;

    public function setUp()
    {
        $this->livestreamServiceMock = $this->createMock(LivestreamService::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $containerMock = $this->createMock(ContainerInterface::class);
        $kernelMock = $this->createMock(KernelInterface::class);
        $kernelMock->expects($this->any())->method('getEnvironment')->willReturn('phpunit');
        $kernelMock->expects($this->any())->method('getBundles')->willReturn([]);
        $kernelMock->expects($this->any())->method('getContainer')->willReturn($containerMock);

        $schedulerExecuteCommand = new SchedulerExecuteCommand(
            $this->loggerMock,
            $this->livestreamServiceMock
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
        $this->livestreamServiceMock->expects($this->once())
            ->method('getStreamToExecute')
            ->willReturn($streamSchedule);
        $this->livestreamServiceMock->expects($this->once())->method('sendLivestreamCommand');
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
        $this->livestreamServiceMock->expects($this->once())
            ->method('getStreamToExecute')
            ->willReturn($streamSchedule);
        $this->livestreamServiceMock->expects($this->once())
            ->method('sendLivestreamCommand')
            ->willThrowException(PublishMessageFailedException::forMessage('message', ['payload']));
        $this->loggerMock->expects($this->atLeastOnce())->method('error');

        $this->commandTester->execute([SchedulerExecuteCommand::COMMAND_SCHEDULER_EXECUTE]);
    }

    /**
     * @covers ::execute
     */
    public function testExecuteNothingToExecute()
    {
        $this->livestreamServiceMock->expects($this->once())
            ->method('getStreamToExecute')
            ->willReturn(null);
        $this->commandTester->execute([SchedulerExecuteCommand::COMMAND_SCHEDULER_EXECUTE]);
        $this->addToAssertionCount(1);
    }

    /**
     * @covers ::execute
     */
    public function testExecuteConflictingStreams()
    {
        $this->livestreamServiceMock->expects($this->once())
            ->method('getStreamToExecute')
            ->willThrowException(ConflictingScheduledStreamsException::multipleSchedules([new StreamSchedule()]));
        $this->loggerMock->expects($this->once())->method('warning');

        $this->commandTester->execute([SchedulerExecuteCommand::COMMAND_SCHEDULER_EXECUTE]);
    }
}
