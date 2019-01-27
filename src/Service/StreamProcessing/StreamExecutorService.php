<?php
declare(strict_types=1);

namespace App\Service\StreamProcessing;

use App\Entity\ScheduleLog;
use App\Entity\StreamSchedule;
use App\Exception\ConflictingScheduledStreamsException;
use App\Exception\CouldNotModifyStreamScheduleException;
use App\Exception\ExecutorCouldNotExecuteStreamException;
use App\Repository\StreamScheduleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class StreamExecutorService
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var StreamScheduleRepository */
    private $streamScheduleRepository;

    /** @var LoggerInterface */
    private $logger;

    /** @var StopStreamService */
    private $stopStreamService;

    /** @var StartStreamService */
    private $startStreamService;

    /**
     * StreamExecutorService constructor.
     * @param EntityManagerInterface $entityManager
     * @param StreamScheduleRepository $streamScheduleRepository
     * @param LoggerInterface $logger
     * @param StopStreamService $stopStreamService
     * @param StartStreamService $startStreamService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        StreamScheduleRepository $streamScheduleRepository,
        LoggerInterface $logger,
        StopStreamService $stopStreamService,
        StartStreamService $startStreamService
    ) {
        $this->entityManager = $entityManager;
        $this->streamScheduleRepository = $streamScheduleRepository;
        $this->logger = $logger;
        $this->stopStreamService = $stopStreamService;
        $this->startStreamService = $startStreamService;
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
            $this->markConflictingStreamsAsWrecked($streamsToBeExecuted);
            throw ConflictingScheduledStreamsException::multipleSchedules($streamSchedules);
        }

        return $streamsToBeExecuted ? current($streamsToBeExecuted) : null;
    }

    /**
     * @param StreamSchedule $streamSchedule
     * @throws ExecutorCouldNotExecuteStreamException
     * @throws CouldNotModifyStreamScheduleException
     */
    public function start(StreamSchedule $streamSchedule)
    {
        try {
            $this->startStreamService->process();
            $streamSchedule->setLastExecution(new \DateTime());
            $scheduleLog = new ScheduleLog($streamSchedule, true, 'Livestream successfully started');
            $streamSchedule->addScheduleLog($scheduleLog);
            $streamSchedule->setIsRunning(true);
        } catch (\Exception $exception) {
            $streamSchedule->setIsRunning(false);
            $streamSchedule->setWrecked(true);
            $scheduleLog = new ScheduleLog($streamSchedule, false, $exception->getMessage());
            $streamSchedule->addScheduleLog($scheduleLog);
            throw ExecutorCouldNotExecuteStreamException::couldNotStartLivestream($exception);
        } finally {
            $this->streamScheduleRepository->save($streamSchedule);
        }
    }

    /**
     * @param StreamSchedule $streamSchedule
     * @throws CouldNotModifyStreamScheduleException
     * @throws ExecutorCouldNotExecuteStreamException
     */
    public function stop(StreamSchedule $streamSchedule)
    {
        try {
            $this->stopStreamService->process();
            $streamSchedule->setIsRunning(false);
            $scheduleLog = new ScheduleLog($streamSchedule, true, 'Livestream successfully stopped');
            $streamSchedule->addScheduleLog($scheduleLog);
        } catch (\Exception $exception) {
            $streamSchedule->setIsRunning(true);
            $streamSchedule->setWrecked(true);
            $scheduleLog = new ScheduleLog($streamSchedule, false, $exception->getMessage());
            $streamSchedule->addScheduleLog($scheduleLog);
            throw ExecutorCouldNotExecuteStreamException::couldNotStopLivestream($exception);
        } finally {
            $this->streamScheduleRepository->save($streamSchedule);
        }
    }

    /**
     * @param StreamSchedule[] $streamSchedules
     */
    private function markConflictingStreamsAsWrecked(array $streamSchedules)
    {
        foreach ($streamSchedules as $streamSchedule) {
            $streamSchedule->setWrecked(true);
            $streamSchedule->setIsRunning(false);
            $scheduleLog = new ScheduleLog($streamSchedule, false, 'Conflicting scheduled streams');
            $streamSchedule->addScheduleLog($scheduleLog);
            $this->entityManager->persist($streamSchedule);
        }
        $this->entityManager->flush();
    }
}
