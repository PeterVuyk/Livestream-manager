<?php
declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Camera;
use App\Entity\StreamSchedule;
use App\Exception\StreamSchedule\ConflictingScheduledStreamsException;
use App\Exception\Repository\CouldNotFindMainCameraException;
use App\Exception\Livestream\CouldNotStartLivestreamException;
use App\Exception\Livestream\CouldNotStopLivestreamException;
use App\Exception\Messaging\PublishMessageFailedException;
use App\Messaging\Dispatcher\MessagingDispatcher;
use App\Repository\CameraRepository;
use App\Repository\StreamScheduleRepository;
use App\Service\LivestreamService;
use App\Service\StateMachineInterface;
use App\Service\StreamProcessing\StreamScheduleExecutor;
use App\Service\StreamProcessing\StreamStateMachine;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Service\LivestreamService
 * @covers ::<!public>
 * @covers ::__construct()
 * @uses \App\Entity\Camera
 * @uses \App\Entity\StreamSchedule
 * @uses \App\Service\LivestreamService
 * @uses \App\Messaging\Library\Command\StartLivestreamCommand
 * @uses \App\Messaging\Library\Command\StopLivestreamCommand
 */
class LivestreamServiceTest extends TestCase
{
    /** @var StateMachineInterface|MockObject */
    private $streamStateMachineMock;

    /** @var CameraRepository|MockObject */
    private $cameraRepositoryMock;

    /** @var LivestreamService */
    private $livestreamService;

    /** @var MessagingDispatcher|MockObject */
    private $messagingDispatcherMock;

    /** @var StreamScheduleExecutor|MockObject */
    private $streamScheduleExecutorMock;

    /** @var StreamScheduleRepository|MockObject */
    private $streamScheduleRepositoryMock;

    public function setUp()
    {
        $this->streamStateMachineMock = $this->createMock(StreamStateMachine::class);
        $this->cameraRepositoryMock = $this->createMock(CameraRepository::class);
        $this->messagingDispatcherMock = $this->createMock(MessagingDispatcher::class);
        $this->streamScheduleExecutorMock = $this->createMock(StreamScheduleExecutor::class);
        $this->streamScheduleRepositoryMock = $this->createMock(StreamScheduleRepository::class);
        $this->livestreamService = new LivestreamService(
            $this->streamStateMachineMock,
            $this->cameraRepositoryMock,
            $this->messagingDispatcherMock,
            $this->streamScheduleExecutorMock,
            $this->streamScheduleRepositoryMock
        );
    }

    /**
     * @throws CouldNotFindMainCameraException
     * @covers ::getMainCameraStatus
     */
    public function testGetMainCameraStatusSuccess()
    {
        $this->cameraRepositoryMock->expects($this->once())->method('getMainCamera')->willReturn(new Camera());

        $camera = $this->livestreamService->getMainCameraStatus();
        $this->assertInstanceOf(Camera::class, $camera);
    }

    /**
     * @throws CouldNotFindMainCameraException
     * @covers ::getMainCameraStatus
     */
    public function testGetMainCameraStatusFailed()
    {
        $this->expectException(CouldNotFindMainCameraException::class);
        $this->cameraRepositoryMock->expects($this->once())->method('getMainCamera');

        $this->livestreamService->getMainCameraStatus();
    }

    /**
     * @covers ::getStreamToExecute
     * @throws ConflictingScheduledStreamsException
     */
    public function testGetStreamToExecuteNothingToExecute()
    {
        $this->streamScheduleRepositoryMock->expects($this->once())
            ->method('findActiveSchedules')
            ->willReturn([]);
        $this->assertNull($this->livestreamService->getStreamToExecute());
    }

    /**
     * @throws ConflictingScheduledStreamsException
     * @covers ::getStreamToExecute
     */
    public function testGetStreamToExecuteOneStreamForExecution()
    {
        $this->streamScheduleRepositoryMock->expects($this->once())
            ->method('findActiveSchedules')
            ->willReturn([$this->getStreamToBeStarted()]);

        $streamSchedule = $this->livestreamService->getStreamToExecute();
        $this->assertInstanceOf(StreamSchedule::class, $streamSchedule);
    }

    /**
     * @throws \Exception
     * @covers ::getStreamToExecute
     */
    public function testGetStreamToExecuteConflictingStreams()
    {
        $this->expectException(ConflictingScheduledStreamsException::class);

        $streamSchedule = new StreamSchedule();
        $streamSchedule->setExecutionTime(new \DateTime('- 1 minutes'));
        $streamSchedule->setOnetimeExecutionDate(new \DateTime('- 1 minutes'));
        $this->streamScheduleRepositoryMock->expects($this->once())
            ->method('findActiveSchedules')
            ->willReturn([$this->getStreamToBeStarted(), $this->getStreamToBeStarted()]);

        $this->livestreamService->getStreamToExecute();
    }

    /**
     * @dataProvider sendLivestreamCommandProvider
     * @param StreamSchedule $streamSchedule
     * @param string $exceptionClass
     * @throws CouldNotFindMainCameraException
     * @throws CouldNotStartLivestreamException
     * @throws CouldNotStopLivestreamException
     * @throws PublishMessageFailedException
     * @covers ::sendLivestreamCommand
     */
    public function testSendLivestreamCommandStart(StreamSchedule $streamSchedule, ?string $exceptionClass)
    {
        $status = true;
        if ($exceptionClass) {
            $this->expectException($exceptionClass);
            $status = false;
        }
        $this->cameraRepositoryMock->expects($this->once())->method('getMainCamera')->willReturn(new Camera());
        $this->streamStateMachineMock->expects($this->once())->method('can')->willReturn($status);
        $this->messagingDispatcherMock->expects($this->any())->method('sendMessage');

        $this->livestreamService->sendLivestreamCommand($streamSchedule);
    }

    public function sendLivestreamCommandProvider()
    {
        return [
            [
                $this->getStreamToBeStarted(),
                CouldNotStartLivestreamException::class,
            ], [
                $this->getStreamToBeStopped(),
                CouldNotStopLivestreamException::class,
            ], [
                $this->getStreamToBeStarted(),
                null,
            ], [
                $this->getStreamToBeStopped(),
                null,
            ]
        ];
    }

    private function getStreamToBeStarted(): StreamSchedule
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setChannel('channel');
        $streamSchedule->setExecutionTime(new \DateTime('- 1 minutes'));
        $streamSchedule->setOnetimeExecutionDate(new \DateTime('- 1 minutes'));
        return $streamSchedule;
    }

    private function getStreamToBeStopped(): StreamSchedule
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setChannel('channel');
        $streamSchedule->setIsRunning(true);
        $streamSchedule->setStreamDuration(5);
        $streamSchedule->setLastExecution(new \DateTime('- 10 minutes'));
        return $streamSchedule;
    }

}
