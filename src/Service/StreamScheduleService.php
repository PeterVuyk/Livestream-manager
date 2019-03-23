<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\ScheduleLog;
use App\Entity\StreamSchedule;
use App\Repository\StreamScheduleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class StreamScheduleService
{
    /** @var StreamScheduleRepository */
    private $streamScheduleRepository;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param StreamScheduleRepository $streamScheduleRepository
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        StreamScheduleRepository $streamScheduleRepository,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ) {
        $this->streamScheduleRepository = $streamScheduleRepository;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * @return StreamSchedule[]
     */
    public function getStreamsToExecute(): array
    {
        $streamSchedules = $this->streamScheduleRepository->findActiveSchedules();
        $streamsPlannedToExecuted = [];
        foreach ($streamSchedules as $streamSchedule) {
            if ($streamSchedule->streamTobeStarted() || $streamSchedule->streamToBeStopped()) {
                $streamsPlannedToExecuted[$streamSchedule->getChannel()][] = $streamSchedule;
            }
        }

        $streamsToExecute = [];
        foreach ($streamsPlannedToExecuted as $streamsPerSchedule) {
            if (count($streamsPerSchedule) > 1) {
                $this->markConflictingStreamsAsWrecked($streamsPerSchedule);
                $this->logger->warning('conflicting schedules, could not process');
                continue;
            }
            $streamsToExecute[] = current($streamsPerSchedule);
        }

        if (count($streamsPlannedToExecuted) > 1) {
            $this->markConflictingStreamsAsWrecked($streamsPlannedToExecuted);
        }

        return $streamsToExecute;
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
    }
}
