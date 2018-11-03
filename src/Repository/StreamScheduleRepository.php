<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\StreamSchedule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bridge\Doctrine\RegistryInterface;

class StreamScheduleRepository extends ServiceEntityRepository
{
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
    public function save(StreamSchedule $streamSchedule)
    {
        $this->getEntityManager()->persist($streamSchedule);
        $this->getEntityManager()->flush();
    }

    /**
     * @param StreamSchedule $streamSchedule
     * @throws ORMException
     */
    public function remove(StreamSchedule $streamSchedule)
    {
        $this->getEntityManager()->remove($streamSchedule);
        $this->getEntityManager()->flush();
    }
}
