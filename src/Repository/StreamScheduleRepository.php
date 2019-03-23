<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\StreamSchedule;
use App\Exception\Repository\CouldNotModifyStreamScheduleException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Symfony\Bridge\Doctrine\RegistryInterface;

class StreamScheduleRepository extends ServiceEntityRepository
{
    const ID_COLUMN = 'id';
    const ONETIME_EXECUTION_DATE_COLUMN = 'onetime_execution_date';

    /**
     * UserRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, StreamSchedule::class);
    }

    /**
     * @param StreamSchedule $streamSchedule
     * @throws CouldNotModifyStreamScheduleException
     */
    public function save(StreamSchedule $streamSchedule): void
    {
        try {
            $this->getEntityManager()->persist($streamSchedule);
            $this->getEntityManager()->flush();
        } catch (ORMException $exception) {
            throw CouldNotModifyStreamScheduleException::forError($exception);
        }
    }

    /**
     * @param StreamSchedule $streamSchedule
     * @throws CouldNotModifyStreamScheduleException
     */
    public function remove(StreamSchedule $streamSchedule): void
    {
        try {
            $this->getEntityManager()->remove($streamSchedule);
            $this->getEntityManager()->flush();
        } catch (ORMException $exception) {
            throw CouldNotModifyStreamScheduleException::forError($exception);
        }
    }

    /**
     * @param string $channel
     * @return StreamSchedule[]
     */
    public function getActiveOnetimeScheduledItems(string $channel): array
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->where('s.onetimeExecutionDate IS NOT NULL')
            ->andWhere('s.executionDay IS NULL')
            ->andWhere('s.executionTime IS NULL');
        if ($channel !== 'Admin') {
            $queryBuilder->andWhere('s.channel = :channel')->setParameter('channel', $channel);
        }
        return $queryBuilder
            ->orderBy('s.onetimeExecutionDate', 'ASC')->getQuery()->getResult();
    }

    /**
     * @param string $channel
     * @return StreamSchedule[]
     */
    public function getRecurringScheduledItems(string $channel): array
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->where('s.onetimeExecutionDate IS NULL')
            ->andWhere('s.executionDay IS NOT NULL')
            ->andWhere('s.executionTime IS NOT NULL');
        if ($channel !== 'Admin') {
            $queryBuilder->andWhere('s.channel = :channel')->setParameter('channel', $channel);
        }
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param string $id
     * @return null|StreamSchedule|object
     */
    public function getScheduledItem(string $id): ?StreamSchedule
    {
        return $this->findOneBy([self::ID_COLUMN => $id]);
    }

    /**
     * @return StreamSchedule[]
     */
    public function findActiveSchedules(): array
    {
        return $this->findBy(['disabled' => false, 'wrecked' => false]);
    }

    /**
     * @return null|object|StreamSchedule
     */
    public function findRunningSchedule(): ?StreamSchedule
    {
        return $this->findOneBy(['isRunning' => true]);
    }

    /**
     * @param string $channelName
     * @return StreamSchedule|null|object
     */
    public function getStreamToExecuteByChannelName(string $channelName): ?StreamSchedule
    {
        return $this->findOneBy(['channel' => $channelName]);
    }
}
