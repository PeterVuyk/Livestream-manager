<?php
declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\StopLivestreamCommand;
use App\Entity\StreamSchedule;
use App\Exception\CouldNotModifyCameraException;
use App\Exception\ExecutorCouldNotExecuteStreamException;
use App\Repository\StreamScheduleRepository;
use App\Service\StreamProcessing\StatusLivestream;
use App\Service\StreamProcessing\StopLivestream;
use App\Service\StreamProcessing\StreamScheduleExecutor;
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
 * @uses \App\Service\StreamProcessing\StopLivestream
 * @uses \App\Service\StreamProcessing\StatusLivestream
 * @uses \App\Entity\StreamSchedule
 * @uses \App\Command\StopLivestreamCommand
 */
class StopLivestreamCommandTest extends TestCase
{
    /** @var StopLivestream|MockObject */
    private $stopLivestream;

    /** @var StreamScheduleRepository|MockObject */
    private $streamScheduleRepositoryMock;

    /** @var StreamScheduleExecutor|MockObject */
    private $streamScheduleExecutorMock;

    /** @var LoggerInterface|MockObject */
    private $loggerMock;

    /** @var CommandTester */
    private $commandTester;

    public function setUp()
    {
        $this->stopLivestream = $this->createMock(StopLivestream::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->streamScheduleExecutorMock = $this->createMock(StreamScheduleExecutor::class);
        $this->streamScheduleRepositoryMock = $this->createMock(StreamScheduleRepository::class);

        $containerMock = $this->createMock(ContainerInterface::class);
        $kernelMock = $this->createMock(KernelInterface::class);
        $kernelMock->expects($this->any())->method('getEnvironment')->willReturn('phpunit');
        $kernelMock->expects($this->any())->method('getBundles')->willReturn([]);
        $kernelMock->expects($this->any())->method('getContainer')->willReturn($containerMock);

        $stopLivestreamCommand = new StopLivestreamCommand(
            $this->stopLivestream,
            $this->streamScheduleRepositoryMock,
            $this->streamScheduleExecutorMock,
            $this->loggerMock
        );

        $application = new Application($kernelMock);
        $application->add($stopLivestreamCommand);

        $stopLivestreamCommandMock = $application->find(StopLivestreamCommand::COMMAND_STOP_STREAM);
        $this->commandTester = new CommandTester($stopLivestreamCommandMock);
    }

    /**
     * @covers ::execute
     */
    public function testExecuteWithoutRunningStreamScheduleSuccess()
    {
        $this->streamScheduleRepositoryMock->expects($this->once())->method('findRunningSchedule');
        $this->stopLivestream->expects($this->once())->method('process');
        $this->loggerMock->expects($this->never())->method('error');

        $this->commandTester->execute([StopLivestreamCommand::COMMAND_STOP_STREAM]);
    }

    /**
     * @covers ::execute
     */
    public function testExecuteWithoutRunningStreamScheduleFailed()
    {
        $this->streamScheduleRepositoryMock->expects($this->once())->method('findRunningSchedule');
        $this->stopLivestream->expects($this->once())
            ->method('process')
            ->willThrowException(new CouldNotModifyCameraException());
        $this->loggerMock->expects($this->atLeastOnce())->method('error');

        $this->commandTester->execute([StopLivestreamCommand::COMMAND_STOP_STREAM]);
    }

    public function testExecuteWithRunningStreamScheduleSuccess()
    {
        $this->streamScheduleRepositoryMock->expects($this->once())
            ->method('findRunningSchedule')
            ->willReturn(new StreamSchedule());

        $this->streamScheduleExecutorMock->expects($this->once())->method('stop');
        $this->loggerMock->expects($this->never())->method('error');

        $this->commandTester->execute([StopLivestreamCommand::COMMAND_STOP_STREAM]);
    }

    public function testExecuteWithRunningStreamScheduleFailed()
    {
        $this->streamScheduleRepositoryMock->expects($this->once())
            ->method('findRunningSchedule')
            ->willReturn(new StreamSchedule());

        $this->streamScheduleExecutorMock->expects($this->once())
            ->method('stop')
            ->willThrowException(ExecutorCouldNotExecuteStreamException::couldNotStartLivestream(new \Exception()));
        $this->loggerMock->expects($this->atLeastOnce())->method('error');

        $this->commandTester->execute([StopLivestreamCommand::COMMAND_STOP_STREAM]);
    }
}
