<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\RecurringSchedule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bridge\Doctrine\RegistryInterface;

class RecurringScheduleRepository extends ServiceEntityRepository
{
    /**
     * UserRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, RecurringSchedule::class);
    }

    /**
     * @param RecurringSchedule $recurringSchedule
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(RecurringSchedule $recurringSchedule): void
    {
        $this->getEntityManager()->persist($recurringSchedule);
        $this->getEntityManager()->flush();
    }

    /**
     * @param RecurringSchedule $recurringSchedule
     * @throws ORMException
     */
    public function remove(RecurringSchedule $recurringSchedule): void
    {
        $this->getEntityManager()->remove($recurringSchedule);
        $this->getEntityManager()->flush();
    }

    /**
     * @return RecurringSchedule[]
     */
    public function findActiveCommands(): array
    {
        return $this->findBy(['disabled' => false, 'wrecked' => false], ['priority' => 'DESC']);
    }
}
