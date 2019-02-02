<?php
declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\StartLivestreamCommand;
use App\Entity\Camera;
use App\Exception\PublishMessageFailedException;
use App\Messaging\Dispatcher\MessagingDispatcher;
use App\Service\LivestreamService;
use App\Service\StreamProcessing\StreamStateMachine;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @coversDefaultClass \App\Command\StartLivestreamCommand
 * @covers ::<!public>
 * @covers ::__construct
 * @uses \App\Entity\Camera
 * @uses \App\Messaging\Library\Command\StartLivestreamCommand
 */
class StartLivestreamCommandTest extends TestCase
{
    /** @var MessagingDispatcher|MockObject */
    private $messagingDispatcher;

    /** @var LivestreamService|MockObject */
    private $livestreamService;

    /** @var StreamStateMachine|MockObject */
    private $streamStateMachine;

    /** @var LoggerInterface|MockObject */
    private $logger;

    /** @var CommandTester */
    private $commandTester;

    public function setUp()
    {
        $this->messagingDispatcher = $this->createMock(MessagingDispatcher::class);
        $this->livestreamService = $this->createMock(LivestreamService::class);
        $this->streamStateMachine = $this->createMock(StreamStateMachine::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $containerMock = $this->createMock(ContainerInterface::class);
        $kernelMock = $this->createMock(KernelInterface::class);
        $kernelMock->expects($this->any())->method('getEnvironment')->willReturn('phpunit');
        $kernelMock->expects($this->any())->method('getBundles')->willReturn([]);
        $kernelMock->expects($this->any())->method('getContainer')->willReturn($containerMock);

        $startLivestreamCommand = new StartLivestreamCommand(
            $this->messagingDispatcher,
            $this->logger,
            $this->livestreamService,
            $this->streamStateMachine
        );

        $application = new Application($kernelMock);
        $application->add($startLivestreamCommand);

        $startLivestreamCommandMock = $application->find(StartLivestreamCommand::COMMAND_START_LIVESTREAM);
        $this->commandTester = new CommandTester($startLivestreamCommandMock);
    }

    /**
     * @covers ::execute
     */
    public function testExecuteSuccess()
    {
        $camera = new Camera();
        $camera->setState('inactive');
        $this->livestreamService->expects($this->once())->method('getMainCameraStatus')->willReturn($camera);
        $this->streamStateMachine->expects($this->once())->method('can')->willReturn(true);
        $this->messagingDispatcher->expects($this->once())->method('sendMessage');
        $this->logger->expects($this->never())->method('error');

        $this->commandTester->execute([StartLivestreamCommand::COMMAND_START_LIVESTREAM]);
    }

    /**
     * @covers ::execute
     */
    public function testExecuteDispatchFailed()
    {
        $camera = new Camera();
        $camera->setState('inactive');
        $this->livestreamService->expects($this->once())->method('getMainCameraStatus')->willReturn($camera);
        $this->streamStateMachine->expects($this->once())->method('can')->willReturn(true);
        $this->messagingDispatcher->expects($this->atLeastOnce())
            ->method('sendMessage')
            ->willThrowException(PublishMessageFailedException::forMessage('topic', []));
        $this->logger->expects($this->atLeastOnce())->method('error');

        $this->commandTester->execute([StartLivestreamCommand::COMMAND_START_LIVESTREAM]);
    }
}
