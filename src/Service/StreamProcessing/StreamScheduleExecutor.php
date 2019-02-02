<?php
declare(strict_types=1);

namespace App\Service\StreamProcessing;

use App\Entity\ScheduleLog;
use App\Entity\StreamSchedule;
use App\Exception\CouldNotModifyStreamScheduleException;
use App\Exception\ExecutorCouldNotExecuteStreamException;
use App\Repository\StreamScheduleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class StreamScheduleExecutor
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var StreamScheduleRepository */
    private $streamScheduleRepository;

    /** @var LoggerInterface */
    private $logger;

    /** @var StopLivestream */
    private $stopLivestream;

    /** @var StartLivestream */
    private $startLivestream;

    /**
     * StreamScheduleExecutor constructor.
     * @param EntityManagerInterface $entityManager
     * @param StreamScheduleRepository $streamScheduleRepository
     * @param LoggerInterface $logger
     * @param StopLivestream $stopLivestream
     * @param StartLivestream $startLivestream
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        StreamScheduleRepository $streamScheduleRepository,
        LoggerInterface $logger,
        StopLivestream $stopLivestream,
        StartLivestream $startLivestream
    ) {
        $this->entityManager = $entityManager;
        $this->streamScheduleRepository = $streamScheduleRepository;
        $this->logger = $logger;
        $this->stopLivestream = $stopLivestream;
        $this->startLivestream = $startLivestream;
    }

    /**
     * @param StreamSchedule $streamSchedule
     * @throws ExecutorCouldNotExecuteStreamException
     * @throws CouldNotModifyStreamScheduleException
     */
    public function start(StreamSchedule $streamSchedule)
    {
        try {
            $this->startLivestream->process();
            $streamSchedule->setLastExecution(new \DateTime());
            $scheduleLog = new ScheduleLog($streamSchedule, true, 'Livestream successfully started');
            $streamSchedule->addScheduleLog($scheduleLog);
            $streamSchedule->setIsRunning(true);
            $this->logger->info('Livestream is streaming');
        } catch (\Exception $exception) {
            $this->logger->error(
                'Could not start livestream',
                ['exception' => $exception, 'message' => $exception->getMessage()]
            );

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
            $this->stopLivestream->process();
            $streamSchedule->setIsRunning(false);
            $scheduleLog = new ScheduleLog($streamSchedule, true, 'Livestream successfully stopped');
            $streamSchedule->addScheduleLog($scheduleLog);
            $this->logger->info('Livestream is stopped successfully');
        } catch (\Exception $exception) {
            $this->logger->error(
                'Could not stop livestream',
                ['exception' => $exception, 'message' => $exception->getMessage()]
            );

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
    public function markConflictingStreamsAsWrecked(array $streamSchedules)
    {
        foreach ($streamSchedules as $streamSchedule) {
            $streamSchedule->setWrecked(true);
            $streamSchedule->setIsRunning(false);
            $scheduleLog = new ScheduleLog($streamSchedule, false, 'Conflicting scheduled streams');
            $streamSchedule->addScheduleLog($scheduleLog);
            $this->entityManager->persist($streamSchedule);
        }
        $this->entityManager->flush();
        //TODO: Can entity manager be replaced by the repository itself?
    }
}
