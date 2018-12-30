<?php
declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\SchedulerExecuteCommand;
use App\Entity\StreamSchedule;
use App\Repository\StreamScheduleRepository;
use App\Service\StartStreamService;
use App\Service\StopStreamService;
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
    /** @var StreamScheduleRepository|MockObject */
    private $streamScheduleRepositoryMock;

    /** @var StartStreamService|MockObject */
    private $startStreamServiceMock;

    /** @var StopStreamService|MockObject */
    private $stopStreamServiceMock;

    /** @var LoggerInterface|MockObject */
    private $loggerMock;

    /** @var CommandTester */
    private $commandTester;

    public function setUp()
    {
        $this->streamScheduleRepositoryMock = $this->createMock(StreamScheduleRepository::class);
        $this->startStreamServiceMock = $this->createMock(StartStreamService::class);
        $this->stopStreamServiceMock = $this->createMock(StopStreamService::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $containerMock = $this->createMock(ContainerInterface::class);
        $kernelMock = $this->createMock(KernelInterface::class);
        $kernelMock->expects($this->any())->method('getEnvironment')->willReturn('phpunit');
        $kernelMock->expects($this->any())->method('getBundles')->willReturn([]);
        $kernelMock->expects($this->any())->method('getContainer')->willReturn($containerMock);

        $schedulerExecuteCommand = new SchedulerExecuteCommand(
            $this->streamScheduleRepositoryMock,
            $this->startStreamServiceMock,
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
    public function testExecuteNoSchedulesToExecute()
    {
        $this->streamScheduleRepositoryMock->expects($this->once())->method('findActiveSchedules')->willReturn([]);
        $this->startStreamServiceMock->expects($this->never())->method('process');
        $this->stopStreamServiceMock->expects($this->never())->method('process');

        $this->commandTester->execute([SchedulerExecuteCommand::COMMAND_SCHEDULER_EXECUTE]);
    }

    /**
     * @covers ::execute
     */
    public function testExecuteStartStreamSuccess()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setExecutionTime(new \DateTime('- 1 minutes'));
        $streamSchedule->setOnetimeExecutionDate(new \DateTime('- 1 minutes'));

        $this->streamScheduleRepositoryMock->expects($this->once())->method('findActiveSchedules')->willReturn([$streamSchedule]);
        $this->streamScheduleRepositoryMock->expects($this->once())->method('save');
        $this->startStreamServiceMock->expects($this->once())->method('process');
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
        $streamSchedule->setLastExecution(new \DateTime('- 6 minutes'));

        $this->streamScheduleRepositoryMock->expects($this->once())->method('findActiveSchedules')->willReturn([$streamSchedule]);
        $this->streamScheduleRepositoryMock->expects($this->once())->method('save');
        $this->startStreamServiceMock->expects($this->never())->method('process');
        $this->stopStreamServiceMock->expects($this->once())->method('process');

        $this->commandTester->execute([SchedulerExecuteCommand::COMMAND_SCHEDULER_EXECUTE]);
    }

    /**
     * @covers ::execute
     */
    public function testExecuteStopStreamFailed()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setIsRunning(true);
        $streamSchedule->setStreamDuration(5);
        $streamSchedule->setLastExecution(new \DateTime('- 6 minutes'));

        $this->streamScheduleRepositoryMock->expects($this->once())->method('findActiveSchedules')->willReturn([$streamSchedule]);
        $this->streamScheduleRepositoryMock->expects($this->once())->method('save');
        $this->stopStreamServiceMock->expects($this->once())->method('process')->willThrowException(new \Exception());
        $this->loggerMock->expects($this->atLeastOnce())->method('error');

        $this->commandTester->execute([SchedulerExecuteCommand::COMMAND_SCHEDULER_EXECUTE]);
    }

    /**
     * @covers ::execute
     */
    public function testExecuteStartStreamFailed()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setExecutionTime(new \DateTime('- 1 minutes'));
        $streamSchedule->setOnetimeExecutionDate(new \DateTime('- 1 minutes'));

        $this->streamScheduleRepositoryMock->expects($this->once())->method('findActiveSchedules')->willReturn([$streamSchedule]);
        $this->streamScheduleRepositoryMock->expects($this->once())->method('save');
        $this->startStreamServiceMock->expects($this->once())->method('process')->willThrowException(new \Exception());
        $this->loggerMock->expects($this->atLeastOnce())->method('error');

        $this->commandTester->execute([SchedulerExecuteCommand::COMMAND_SCHEDULER_EXECUTE]);
    }

    /**
     * @covers ::execute
     */
    public function testExecuteFailedSavingStreamSchedule()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setExecutionTime(new \DateTime('- 1 minutes'));
        $streamSchedule->setOnetimeExecutionDate(new \DateTime('- 1 minutes'));

        $this->streamScheduleRepositoryMock->expects($this->once())->method('findActiveSchedules')->willReturn([$streamSchedule]);
        $this->streamScheduleRepositoryMock->expects($this->once())->method('save')->willThrowException(new ORMException());
        $this->startStreamServiceMock->expects($this->once())->method('process');
        $this->loggerMock->expects($this->atLeastOnce())->method('error');

        $this->commandTester->execute([SchedulerExecuteCommand::COMMAND_SCHEDULER_EXECUTE]);
    }
}
