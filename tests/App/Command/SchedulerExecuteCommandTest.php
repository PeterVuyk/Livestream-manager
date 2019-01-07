<?php
declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\SchedulerExecuteCommand;
use App\Entity\StreamSchedule;
use App\Exception\ConflictingScheduledStreamsException;
use App\Exception\CouldNotModifyStreamScheduleException;
use App\Exception\ExecutorCouldNotExecuteStreamException;
use App\Repository\StreamScheduleRepository;
use App\Service\StartStreamService;
use App\Service\StopStreamService;
use App\Service\StreamExecutorService;
use Doctrine\ORM\ORMException;
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
 * @uses \App\Repository\StreamScheduleRepository
 * @uses \App\Service\StartStreamService
 * @uses \App\Service\StopStreamService
 * @uses \App\Entity\StreamSchedule
 * @uses \App\Entity\ScheduleLog
 */
class SchedulerExecuteCommandTest extends TestCase
{
    /** @var StreamExecutorService|MockObject */
    private $streamExecutorService;

    /** @var StopStreamService|MockObject */
    private $stopStreamServiceMock;

    /** @var LoggerInterface|MockObject */
    private $loggerMock;

    /** @var CommandTester */
    private $commandTester;

    public function setUp()
    {
        $this->streamExecutorService = $this->createMock(StreamExecutorService::class);
        $this->stopStreamServiceMock = $this->createMock(StopStreamService::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $containerMock = $this->createMock(ContainerInterface::class);
        $kernelMock = $this->createMock(KernelInterface::class);
        $kernelMock->expects($this->any())->method('getEnvironment')->willReturn('phpunit');
        $kernelMock->expects($this->any())->method('getBundles')->willReturn([]);
        $kernelMock->expects($this->any())->method('getContainer')->willReturn($containerMock);

        $schedulerExecuteCommand = new SchedulerExecuteCommand(
            $this->streamExecutorService,
            $this->stopStreamServiceMock,
            $this->loggerMock
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
        $this->streamExecutorService->expects($this->once())->method('getStreamToExecute')->willReturn($streamSchedule);
        $this->streamExecutorService->expects($this->once())->method('start');
        $this->stopStreamServiceMock->expects($this->never())->method('process');

        $this->commandTester->execute([SchedulerExecuteCommand::COMMAND_SCHEDULER_EXECUTE]);
    }

    /**
     * @covers ::execute
     */
    public function testExecuteStopStreamSuccess()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setIsRunning(true);
        $streamSchedule->setStreamDuration(5);
        $streamSchedule->setLastExecution(new \DateTime('- 10 minutes'));
        $this->streamExecutorService->expects($this->once())->method('getStreamToExecute')->willReturn($streamSchedule);
        $this->streamExecutorService->expects($this->once())->method('stop');

        $this->commandTester->execute([SchedulerExecuteCommand::COMMAND_SCHEDULER_EXECUTE]);
    }

    /**
     * @covers ::execute
     */
    public function testExecuteStopStreamCouldNotExecute()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setIsRunning(true);
        $streamSchedule->setStreamDuration(5);
        $streamSchedule->setLastExecution(new \DateTime('- 10 minutes'));
        $this->streamExecutorService->expects($this->once())->method('getStreamToExecute')->willReturn($streamSchedule);
        $this->streamExecutorService->expects($this->once())
            ->method('stop')
            ->willThrowException(ExecutorCouldNotExecuteStreamException::couldNotStopLivestream(new \Exception()));
        $this->loggerMock->expects($this->once())->method('error');

        $this->commandTester->execute([SchedulerExecuteCommand::COMMAND_SCHEDULER_EXECUTE]);
    }

    /**
     * @covers ::execute
     */
    public function testExecuteStopStreamCouldNotModifyStreamSchedule()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setIsRunning(true);
        $streamSchedule->setStreamDuration(5);
        $streamSchedule->setLastExecution(new \DateTime('- 10 minutes'));
        $this->streamExecutorService->expects($this->once())->method('getStreamToExecute')->willReturn($streamSchedule);
        $this->streamExecutorService->expects($this->once())
            ->method('stop')
            ->willThrowException(CouldNotModifyStreamScheduleException::forError(new ORMException()));
        $this->loggerMock->expects($this->once())->method('error');

        $this->commandTester->execute([SchedulerExecuteCommand::COMMAND_SCHEDULER_EXECUTE]);
    }

    /**
     * @covers ::execute
     */
    public function testExecuteNothingToExecute()
    {
        $this->streamExecutorService->expects($this->once())->method('getStreamToExecute')->willReturn(null);
        $this->commandTester->execute([SchedulerExecuteCommand::COMMAND_SCHEDULER_EXECUTE]);
        $this->addToAssertionCount(1);
    }

    /**
     * @covers ::execute
     */
    public function testExecuteFailedGettingStreams()
    {
        $this->streamExecutorService->expects($this->once())
            ->method('getStreamToExecute')
            ->willThrowException(ConflictingScheduledStreamsException::multipleSchedules([new StreamSchedule()]));
        $this->loggerMock->expects($this->once())->method('error');
        $this->stopStreamServiceMock->expects($this->once())->method('process');

        $this->commandTester->execute([SchedulerExecuteCommand::COMMAND_SCHEDULER_EXECUTE]);
    }
}
