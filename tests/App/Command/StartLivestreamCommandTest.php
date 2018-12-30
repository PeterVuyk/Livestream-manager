<?php
declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\StartLivestreamCommand;
use App\Service\StartStreamService;
use App\Service\StatusStreamService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @coversDefaultClass \App\Command\StartLivestreamCommand
 * @covers ::<!public>
 * @covers ::__construct
 * @uses \App\Service\StartStreamService
 * @uses \App\Service\StatusStreamService
 * @uses \App\Entity\StreamSchedule
 */
class StartLivestreamCommandTest extends TestCase
{
    /** @var StartStreamService|MockObject */
    private $startStreamServiceMock;

    /** @var StatusStreamService|MockObject */
    private $statusStreamServiceMock;

    /** @var CommandTester */
    private $commandTester;

    public function setUp()
    {
        $this->startStreamServiceMock = $this->createMock(StartStreamService::class);
        $this->statusStreamServiceMock = $this->createMock(StatusStreamService::class);

        $containerMock = $this->createMock(ContainerInterface::class);
        $kernelMock = $this->createMock(KernelInterface::class);
        $kernelMock->expects($this->any())->method('getEnvironment')->willReturn('phpunit');
        $kernelMock->expects($this->any())->method('getBundles')->willReturn([]);
        $kernelMock->expects($this->any())->method('getContainer')->willReturn($containerMock);

        $startLivestreamCommand = new StartLivestreamCommand(
            $this->startStreamServiceMock,
            $this->statusStreamServiceMock
        );

        $application = new Application($kernelMock);
        $application->add($startLivestreamCommand);

        $startLivestreamCommandMock = $application->find(StartLivestreamCommand::COMMAND_START_STREAM);
        $this->commandTester = new CommandTester($startLivestreamCommandMock);
    }

    /**
     * @covers ::execute
     */
    public function testExecuteSuccess()
    {
        $this->statusStreamServiceMock->expects($this->once())->method('isRunning')->willReturn(false);
        $this->startStreamServiceMock->expects($this->once())->method('process');

        $this->commandTester->execute([StartLivestreamCommand::COMMAND_START_STREAM]);
    }

    /**
     * @covers ::execute
     */
    public function testExecuteStreamAlreadyRunning()
    {
        $this->statusStreamServiceMock->expects($this->once())->method('isRunning')->willReturn(true);
        $this->startStreamServiceMock->expects($this->never())->method('process');

        $this->commandTester->execute([StartLivestreamCommand::COMMAND_START_STREAM]);
    }
}
