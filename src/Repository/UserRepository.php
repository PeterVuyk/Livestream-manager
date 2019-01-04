<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\Exception\CouldNotModifyUserException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Symfony\Bridge\Doctrine\RegistryInterface;

class UserRepository extends ServiceEntityRepository
{
    /**
     * UserRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param User $user
     * @throws CouldNotModifyUserException
     */
    public function save(User $user): void
    {
        try {
            $this->getEntityManager()->persist($user);
            $this->getEntityManager()->flush();
        } catch (ORMException $exception) {
            throw CouldNotModifyUserException::forError($exception);
        }
    }

    /**
     * @param User $user
     * @throws CouldNotModifyUserException
     */
    public function remove(User $user): void
    {
        try {
            $this->getEntityManager()->remove($user);
            $this->getEntityManager()->flush();
        } catch (ORMException $exception) {
            throw CouldNotModifyUserException::forError($exception);
        }
    }
}
