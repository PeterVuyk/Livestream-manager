<?php
declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\StopLivestreamCommand;
use App\Service\StreamProcessing\StatusStreamService;
use App\Service\StreamProcessing\StopStreamService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @coversDefaultClass \App\Command\StopLivestreamCommand
 * @covers ::<!public>
 * @covers ::__construct
 * @uses \App\Service\StreamProcessing\StopStreamService
 * @uses \App\Service\StreamProcessing\StatusStreamService
 * @uses \App\Entity\StreamSchedule
 */
class StopLivestreamCommandTest extends TestCase
{
    /** @var StopStreamService|MockObject */
    private $stopStreamServiceMock;

    /** @var StatusStreamService|MockObject */
    private $statusStreamServiceMock;

    /** @var CommandTester */
    private $commandTester;

    public function setUp()
    {
        $this->stopStreamServiceMock = $this->createMock(StopStreamService::class);
        $this->statusStreamServiceMock = $this->createMock(StatusStreamService::class);

        $containerMock = $this->createMock(ContainerInterface::class);
        $kernelMock = $this->createMock(KernelInterface::class);
        $kernelMock->expects($this->any())->method('getEnvironment')->willReturn('phpunit');
        $kernelMock->expects($this->any())->method('getBundles')->willReturn([]);
        $kernelMock->expects($this->any())->method('getContainer')->willReturn($containerMock);

        $stopLivestreamCommand = new StopLivestreamCommand(
            $this->stopStreamServiceMock,
            $this->statusStreamServiceMock
        );

        $application = new Application($kernelMock);
        $application->add($stopLivestreamCommand);

        $stopLivestreamCommandMock = $application->find(StopLivestreamCommand::COMMAND_STOP_STREAM);
        $this->commandTester = new CommandTester($stopLivestreamCommandMock);
    }

    /**
     * @covers ::execute
     */
    public function testExecuteSuccess()
    {
        $this->statusStreamServiceMock->expects($this->once())->method('isRunning')->willReturn(true);
        $this->stopStreamServiceMock->expects($this->once())->method('process');

        $this->commandTester->execute([StopLivestreamCommand::COMMAND_STOP_STREAM]);
    }

    /**
     * @covers ::execute
     */
    public function testExecuteStreamAlreadyRunning()
    {
        $this->statusStreamServiceMock->expects($this->once())->method('isRunning')->willReturn(false);
        $this->stopStreamServiceMock->expects($this->never())->method('process');

        $this->commandTester->execute([StopLivestreamCommand::COMMAND_STOP_STREAM]);
    }
}
