<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\StreamSchedule;
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
}
