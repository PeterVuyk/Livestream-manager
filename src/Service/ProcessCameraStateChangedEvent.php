<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\ScheduleLog;
use App\Entity\StreamSchedule;
use App\Messaging\Library\Event\CameraStateChangedEvent;
use App\Repository\StreamScheduleRepository;
use Psr\Log\LoggerInterface;

class ProcessCameraStateChangedEvent
{
    /** @var StreamScheduleRepository */
    private $streamScheduleRepository;

    /** @var LoggerInterface */
    private $logger;

    /**
     * ProcessLivestreamCommand constructor.
     * @param StreamScheduleRepository $streamScheduleRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        StreamScheduleRepository $streamScheduleRepository,
        LoggerInterface $logger
    ) {
        $this->streamScheduleRepository = $streamScheduleRepository;
        $this->logger = $logger;
    }

    public function process(CameraStateChangedEvent $message): void
    {
        $streamSchedule = $this->streamScheduleRepository->getStreamToExecuteByChannelName($message->getChannel());
        if (!$streamSchedule instanceof StreamSchedule) {
            return;
        }

        switch ($message->getCameraState()) {
            case 'running':
                $this->markStreamAsRunning($streamSchedule);
                break;
            case 'inactive':
                $this->markStreamAsInactive($streamSchedule);
                break;
            case 'failure':
                $this->markStreamAsFailure($streamSchedule);
                break;
        }
        $this->streamScheduleRepository->save($streamSchedule);
    }

    private function markStreamAsRunning(StreamSchedule $streamSchedule): void
    {
        $streamSchedule->setLastExecution(new \DateTime());
        $scheduleLog = new ScheduleLog($streamSchedule, true, 'Livestream successfully started');
        $streamSchedule->addScheduleLog($scheduleLog);
        $streamSchedule->setIsRunning(true);
    }

    private function markStreamAsInactive(StreamSchedule $streamSchedule): void
    {
        $streamSchedule->setIsRunning(false);
        $scheduleLog = new ScheduleLog($streamSchedule, true, 'Livestream successfully stopped');
        $streamSchedule->addScheduleLog($scheduleLog);
    }

    private function markStreamAsFailure(StreamSchedule $streamSchedule): void
    {
        $streamSchedule->setIsRunning(false);
        $streamSchedule->setWrecked(true);
        $scheduleLog = new ScheduleLog($streamSchedule, false, 'Failure, something went wrong scheduled livestream');
        $streamSchedule->addScheduleLog($scheduleLog);
    }
}
