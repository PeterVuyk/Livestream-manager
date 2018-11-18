<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\StreamSchedule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\OptimisticLockException;
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
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(StreamSchedule $streamSchedule): void
    {
        $this->getEntityManager()->persist($streamSchedule);
        $this->getEntityManager()->flush();
    }

    /**
     * @param StreamSchedule $streamSchedule
     * @throws ORMException
     */
    public function remove(StreamSchedule $streamSchedule): void
    {
        $this->getEntityManager()->remove($streamSchedule);
        $this->getEntityManager()->flush();
    }

    /**
     * @return StreamSchedule[]
     */
    public function getActiveOnetimeScheduledItems(): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.onetimeExecutionDate IS NOT NULL')
//            ->andWhere('s.onetimeExecutionDate > :last')
            ->andWhere('s.executionDay IS NULL')
            ->andWhere('s.executionTime IS NULL')
            ->orderBy('s.onetimeExecutionDate', 'ASC')
//            ->setParameter('last', new \DateTime('-3 hours'), Type::DATETIME)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return StreamSchedule[]
     */
    public function getRecurringScheduledItems(): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.onetimeExecutionDate IS NULL')
            ->andWhere('s.executionDay IS NOT NULL')
            ->andWhere('s.executionTime IS NOT NULL')
            ->getQuery()
            ->getResult();
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
    public function findActiveCommands(): array
    {
        return $this->findBy(['disabled' => false, 'wrecked' => false]);
    }
}
