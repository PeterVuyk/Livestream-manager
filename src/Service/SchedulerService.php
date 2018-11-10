<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\RecurringSchedule;
use App\Exception\RecurringScheduleNotFoundException;
use App\Repository\RecurringScheduleRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class SchedulerService
{
    /** @var RecurringScheduleRepository */
    private $recurringScheduleRepository;

    /**
     * SchedulerService constructor.
     * @param RecurringScheduleRepository $recurringScheduleRepository
     */
    public function __construct(RecurringScheduleRepository $recurringScheduleRepository)
    {
        $this->recurringScheduleRepository = $recurringScheduleRepository;
    }

    /**
     * @return RecurringSchedule[]
     */
    public function getAllScheduledItems(): array
    {
        return $this->recurringScheduleRepository->findBy([], ['priority' => 'DESC']);
    }

    /**
     * @param RecurringSchedule $recurringSchedule
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function saveStream(RecurringSchedule $recurringSchedule): void
    {
        $this->recurringScheduleRepository->save($recurringSchedule);
    }

    /**
     * @param string $id
     * @return RecurringSchedule|object|null
     */
    public function getScheduleById(string $id): ?RecurringSchedule
    {
        return $this->recurringScheduleRepository->findOneBy(['id' => $id]);
    }

    /**
     * @param string $scheduleId
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws RecurringScheduleNotFoundException
     */
    public function toggleDisablingSchedule(string $scheduleId): void
    {
        $recurringSchedule = $this->getScheduleById($scheduleId);
        if (!$recurringSchedule instanceof RecurringSchedule) {
            throw RecurringScheduleNotFoundException::couldNotDisableSchedule($scheduleId);
        }
        $recurringSchedule->setDisabled(!$recurringSchedule->getDisabled());
        $this->saveStream($recurringSchedule);
    }

    /**
     * @param string $scheduleId
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws RecurringScheduleNotFoundException
     */
    public function executeScheduleWithNextExecution(string $scheduleId): void
    {
        $recurringSchedule = $this->getScheduleById($scheduleId);
        if (!$recurringSchedule instanceof RecurringSchedule) {
            throw RecurringScheduleNotFoundException::couldNotRunWithNextExecution($scheduleId);
        }
        $recurringSchedule->setRunWithNextExecution(true);
        $this->saveStream($recurringSchedule);
    }

    /**
     * @param string $scheduleId
     * @throws RecurringScheduleNotFoundException
     * @throws ORMException
     */
    public function removeSchedule(string $scheduleId): void
    {
        $recurringSchedule = $this->getScheduleById($scheduleId);
        if (!$recurringSchedule instanceof RecurringSchedule) {
            throw RecurringScheduleNotFoundException::couldNotRemoveSchedule($scheduleId);
        }
        $this->recurringScheduleRepository->remove($recurringSchedule);
    }

    /**
     * @param string $scheduleId
     * @throws RecurringScheduleNotFoundException
     * @throws ORMException
     */
    public function unwreckSchedule(string $scheduleId): void
    {
        $recurringSchedule = $this->getScheduleById($scheduleId);
        if (!$recurringSchedule instanceof RecurringSchedule) {
            throw RecurringScheduleNotFoundException::couldNotUnwreckSchedule($scheduleId);
        }
        $recurringSchedule->setWrecked(false);
        $this->recurringScheduleRepository->save($recurringSchedule);
    }
}
