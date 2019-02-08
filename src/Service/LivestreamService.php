<?php
declare(strict_types=1);

namespace App\Service;

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
use App\Service\StreamProcessing\StreamScheduleExecutor;
use App\Messaging\Library\Command\StopLivestreamCommand;
use App\Messaging\Library\Command\StartLivestreamCommand;

class LivestreamService
{
    /** @var StateMachineInterface */
    private $streamStateMachine;

    /** @var CameraRepository */
    private $cameraRepository;

    /** @var MessagingDispatcher */
    private $messagingDispatcher;

    /** @var StreamScheduleExecutor */
    private $streamScheduleExecutor;

    /** @var StreamScheduleRepository */
    private $streamScheduleRepository;

    /**
     * LivestreamService constructor.
     * @param StateMachineInterface $streamStateMachine
     * @param CameraRepository $cameraRepository
     * @param MessagingDispatcher $messagingDispatcher
     * @param StreamScheduleExecutor $streamScheduleExecutor
     * @param StreamScheduleRepository $streamScheduleRepository
     */
    public function __construct(
        StateMachineInterface $streamStateMachine,
        CameraRepository $cameraRepository,
        MessagingDispatcher $messagingDispatcher,
        StreamScheduleExecutor $streamScheduleExecutor,
        StreamScheduleRepository $streamScheduleRepository
    ) {
        $this->streamStateMachine = $streamStateMachine;
        $this->cameraRepository = $cameraRepository;
        $this->messagingDispatcher = $messagingDispatcher;
        $this->streamScheduleExecutor = $streamScheduleExecutor;
        $this->streamScheduleRepository = $streamScheduleRepository;
    }

    /**
     * @return Camera
     * @throws CouldNotFindMainCameraException
     */
    public function getMainCameraStatus(): Camera
    {
        $camera = $this->cameraRepository->getMainCamera();
        if (!$camera instanceof Camera) {
            throw CouldNotFindMainCameraException::fromRepository();
        }
        return $camera;
    }

    /**
     * @param StreamSchedule $streamSchedule
     * @throws CouldNotFindMainCameraException
     * @throws CouldNotStartLivestreamException
     * @throws CouldNotStopLivestreamException
     * @throws PublishMessageFailedException
     */
    public function sendLivestreamCommand(StreamSchedule $streamSchedule): void
    {
        $camera = $this->getMainCameraStatus();
        if ($streamSchedule->streamTobeStarted()) {
            $toStarting = $this->streamStateMachine->can($camera, 'to_starting');
            if (!$toStarting) {
                throw CouldNotStartLivestreamException::invalidStateOrCameraStatus($toStarting);
            }
            $this->messagingDispatcher->sendMessage(StartLivestreamCommand::create());
        }
        if ($streamSchedule->streamToBeStopped()) {
            $toStopping = $this->streamStateMachine->can($camera, 'to_stopping');
            if (!$toStopping) {
                throw CouldNotStopLivestreamException::invalidStateOrCameraStatus($toStopping);
            }
            $this->messagingDispatcher->sendMessage(StopLivestreamCommand::create());
        }
    }

    /**
     * @return StreamSchedule
     * @throws ConflictingScheduledStreamsException
     */
    public function getStreamToExecute(): ?StreamSchedule
    {
        $streamSchedules = $this->streamScheduleRepository->findActiveSchedules();
        $streamsToBeExecuted = [];
        foreach ($streamSchedules as $streamSchedule) {
            if ($streamSchedule->streamTobeStarted() || $streamSchedule->streamToBeStopped()) {
                $streamsToBeExecuted[] = $streamSchedule;
            }
        }

        if (count($streamsToBeExecuted) > 1) {
            $this->streamScheduleExecutor->markConflictingStreamsAsWrecked($streamsToBeExecuted);
            throw ConflictingScheduledStreamsException::multipleSchedules($streamSchedules);
        }

        return $streamsToBeExecuted ? current($streamsToBeExecuted) : null;
    }
}
