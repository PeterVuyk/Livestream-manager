<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\StreamSchedule;
use App\Exception\CouldNotModifyStreamScheduleException;
use App\Repository\StreamScheduleRepository;

class ManageScheduleService
{
    /** @var StreamScheduleRepository */
    private $streamScheduleRepository;

    /**
     * @param StreamScheduleRepository $streamScheduleRepository
     */
    public function __construct(StreamScheduleRepository $streamScheduleRepository)
    {
        $this->streamScheduleRepository = $streamScheduleRepository;
    }

    /**
     * @return StreamSchedule[]
     */
    public function getRecurringSchedules(): array
    {
        return $this->streamScheduleRepository->getRecurringScheduledItems();
    }

    /**
     * @return StreamSchedule[]
     */
    public function getOnetimeSchedules(): array
    {
        return $this->streamScheduleRepository->getActiveOnetimeScheduledItems();
    }

    /**
     * @return StreamSchedule[]
     */
    public function getAllSchedules(): array
    {
        return $this->streamScheduleRepository->findAll();
    }

    /**
     * @param string $id
     * @return StreamSchedule|null
     */
    public function getScheduleById(string $id): ?StreamSchedule
    {
        return $this->streamScheduleRepository->getScheduledItem($id);
    }

    /**
     * @param StreamSchedule $streamSchedule
     * @throws CouldNotModifyStreamScheduleException
     */
    public function toggleDisablingSchedule(StreamSchedule $streamSchedule): void
    {
        $streamSchedule->setDisabled(!$streamSchedule->getDisabled());
        $this->saveSchedule($streamSchedule);
    }

    /**
     * @param StreamSchedule $streamSchedule
     * @throws CouldNotModifyStreamScheduleException
     */
    public function unwreckSchedule(StreamSchedule $streamSchedule): void
    {
        $streamSchedule->setWrecked(false);
        $this->saveSchedule($streamSchedule);
    }

    /**
     * @param StreamSchedule $streamSchedule
     * @throws CouldNotModifyStreamScheduleException
     */
    public function removeSchedule(StreamSchedule $streamSchedule): void
    {
        $this->streamScheduleRepository->remove($streamSchedule);
    }

    /**
     * @param StreamSchedule $streamSchedule
     * @throws CouldNotModifyStreamScheduleException
     */
    public function saveSchedule(StreamSchedule $streamSchedule): void
    {
        $this->streamScheduleRepository->save($streamSchedule);
    }
}
