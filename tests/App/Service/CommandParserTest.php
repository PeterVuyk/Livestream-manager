<?php
declare(strict_types=1);

namespace App\Tests\Service;

use App\Command\SchedulerExecuteCommand;
use App\Command\StartLivestreamCommand;
use App\Command\StopLivestreamCommand;
use App\Repository\StreamScheduleRepository;
use App\Service\CommandParser;
use App\Service\StartStreamService;
use App\Service\StopStreamService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\Debug\TraceableEventDispatcher;

class CommandParserTest extends TestCase
{
    /** @var CommandParser */
    private $commandParser;

    /** @var KernelInterface|MockObject */
    private $kernelMock;

    public function setUp()
    {
        $this->kernelMock = $this->createMock(KernelInterface::class);
        $this->commandParser = new CommandParser($this->kernelMock);
    }

    /**
     * @throws \Exception
     */
    public function testGetCommands()
    {
        $loggerMock = $this->createMock(LoggerInterface::class);
        $streamScheduleMock = $this->createMock(StreamScheduleRepository::class);
        $traceableEventDispatcherMock = $this->createMock(TraceableEventDispatcher::class);
        $containerMock = $this->createMock(ContainerInterface::class);
        $containerMock->expects($this->exactly(4))
            ->method('get')
            ->willReturn(
                $traceableEventDispatcherMock,
                new SchedulerExecuteCommand($streamScheduleMock, $loggerMock),
                new StartLivestreamCommand($this->createMock(StartStreamService::class)),
                new StopLivestreamCommand($this->createMock(StopStreamService::class))
            );
        $this->kernelMock->expects($this->any())->method('getContainer')->willReturn($containerMock);

        $parameters = [
            0 => 'console.command.public_alias.App\\Command\\SchedulerExecuteCommand',
            1 => 'console.command.public_alias.App\\Command\\StartLivestreamCommand',
            2 => 'console.command.public_alias.App\\Command\\StopLivestreamCommand',
        ];

        $containerMock->expects($this->any())->method('hasParameter')->willReturn($parameters);
        $containerMock->expects($this->any())->method('getParameter')->willReturn($parameters);
        $bundleMock = $this->createMock(BundleInterface::class);
        $this->kernelMock->expects($this->once())->method('getBundles')->willReturn([$bundleMock]);

        $result = $this->commandParser->getCommands();
        $this->assertArrayHasKey(CommandParser::STREAM_NAMESPACE, $result);
        $this->assertArrayNotHasKey('scheduler', $result);

        $stream = $result[CommandParser::STREAM_NAMESPACE];
        $this->assertArrayHasKey(StartLivestreamCommand::COMMAND_START_STREAM, $stream);
        $this->assertArrayHasKey(StopLivestreamCommand::COMMAND_STOP_STREAM, $stream);
    }
}
