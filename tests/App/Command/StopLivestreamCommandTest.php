<?php
declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\StopLivestreamCommand;
use App\Entity\Camera;
use App\Entity\StreamSchedule;
use App\Exception\CouldNotModifyCameraException;
use App\Exception\ExecutorCouldNotExecuteStreamException;
use App\Exception\PublishMessageFailedException;
use App\Messaging\Dispatcher\MessagingDispatcher;
use App\Repository\StreamScheduleRepository;
use App\Service\LivestreamService;
use App\Service\StreamProcessing\StatusLivestream;
use App\Service\StreamProcessing\StopLivestream;
use App\Service\StreamProcessing\StreamScheduleExecutor;
use App\Service\StreamProcessing\StreamStateMachine;
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
 * @uses \App\Entity\Camera
 * @uses \App\Messaging\Library\Command\StopLivestreamCommand
 */
class StopLivestreamCommandTest extends TestCase
{
    /** @var MessagingDispatcher|MockObject */
    private $messagingDispatcher;

    /** @var LoggerInterface|MockObject */
    private $logger;

    /** @var LivestreamService|MockObject */
    private $livestreamService;

    /** @var StreamStateMachine|MockObject */
    private $streamStateMachine;

    /** @var CommandTester */
    private $commandTester;

    public function setUp()
    {
        $this->messagingDispatcher = $this->createMock(MessagingDispatcher::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->livestreamService= $this->createMock(LivestreamService::class);
        $this->streamStateMachine = $this->createMock(StreamStateMachine::class);

        $containerMock = $this->createMock(ContainerInterface::class);
        $kernelMock = $this->createMock(KernelInterface::class);
        $kernelMock->expects($this->any())->method('getEnvironment')->willReturn('phpunit');
        $kernelMock->expects($this->any())->method('getBundles')->willReturn([]);
        $kernelMock->expects($this->any())->method('getContainer')->willReturn($containerMock);

        $stopLivestreamCommand = new StopLivestreamCommand(
            $this->messagingDispatcher,
            $this->logger,
            $this->livestreamService,
            $this->streamStateMachine
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
        $camera = new Camera();
        $camera->setState('running');
        $this->livestreamService->expects($this->once())->method('getMainCameraStatus')->willReturn($camera);
        $this->streamStateMachine->expects($this->once())->method('can')->willReturn(true);
        $this->messagingDispatcher->expects($this->once())->method('sendMessage');
        $this->logger->expects($this->never())->method('error');

        $this->commandTester->execute([StopLivestreamCommand::COMMAND_STOP_STREAM]);
    }

    /**
     * @covers ::execute
     */
    public function testExecuteDispatchFailed()
    {
        $camera = new Camera();
        $camera->setState('running');
        $this->livestreamService->expects($this->once())->method('getMainCameraStatus')->willReturn($camera);
        $this->streamStateMachine->expects($this->once())->method('can')->willReturn(true);
        $this->messagingDispatcher->expects($this->once())
            ->method('sendMessage')
            ->willThrowException(PublishMessageFailedException::forMessage('topic', []));

        $this->logger->expects($this->atLeastOnce())->method('error');

        $this->commandTester->execute([StopLivestreamCommand::COMMAND_STOP_STREAM]);
    }
}
