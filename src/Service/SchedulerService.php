<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\StreamSchedule;
use App\Exception\StreamScheduleNotFoundException;
use App\Repository\StreamScheduleRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class SchedulerService
{
    /** @var StreamScheduleRepository */
    private $streamScheduleRepository;

    /**
     * SchedulerService constructor.
     * @param StreamScheduleRepository $streamScheduleRepository
     */
    public function __construct(StreamScheduleRepository $streamScheduleRepository)
    {
        $this->streamScheduleRepository = $streamScheduleRepository;
    }

    /**
     * @return StreamSchedule[]
     */
    public function getAllScheduledItems(): array
    {
        return $this->streamScheduleRepository->findAll();
    }

    /**
     * @param StreamSchedule $streamSchedule
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function saveStream(StreamSchedule $streamSchedule): void
    {
        $this->streamScheduleRepository->save($streamSchedule);
    }

    /**
     * @param string $id
     * @return StreamSchedule|object|null
     */
    public function getScheduleById(string $id): ?StreamSchedule
    {
        return $this->streamScheduleRepository->findOneBy(['id' => $id]);
    }

    /**
     * @param string $scheduleId
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws StreamScheduleNotFoundException
     */
    public function toggleDisablingSchedule(string $scheduleId): void
    {
        $streamSchedule = $this->getScheduleById($scheduleId);
        if (!$streamSchedule instanceof StreamSchedule) {
            throw StreamScheduleNotFoundException::couldNotDisableSchedule($scheduleId);
        }
        $streamSchedule->setDisabled(!$streamSchedule->getDisabled());
        $this->saveStream($streamSchedule);
    }

    /**
     * @param string $scheduleId
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws StreamScheduleNotFoundException
     */
    public function executeScheduleWithNextExecution(string $scheduleId): void
    {
        $streamSchedule = $this->getScheduleById($scheduleId);
        if (!$streamSchedule instanceof StreamSchedule) {
            throw StreamScheduleNotFoundException::couldNotRunWithNextExecution($scheduleId);
        }
        $streamSchedule->setRunWithNextExecution(true);
        $this->saveStream($streamSchedule);
    }

    /**
     * @param string $scheduleId
     * @throws StreamScheduleNotFoundException
     * @throws ORMException
     */
    public function removeSchedule(string $scheduleId): void
    {
        $streamSchedule = $this->getScheduleById($scheduleId);
        if (!$streamSchedule instanceof StreamSchedule) {
            throw StreamScheduleNotFoundException::couldNotRemoveSchedule($scheduleId);
        }
        $this->streamScheduleRepository->remove($streamSchedule);
    }
}
