<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Camera;
use App\Entity\StateAwareInterface;
use App\Exception\Repository\CouldNotModifyCameraException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CameraRepository extends ServiceEntityRepository
{
    /**
     * UserRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Camera::class);
    }

    /**
     * @return null|object|StateAwareInterface|Camera
     */
    public function getMainCamera()
    {
        return $this->findOneBy(['camera' => 'mainCamera']);
    }

    /**
     * @param StateAwareInterface $camera
     * @throws CouldNotModifyCameraException
     */
    public function save(StateAwareInterface $camera)
    {
        try {
            $this->getEntityManager()->persist($camera);
            $this->getEntityManager()->flush();
        } catch (ORMException $exception) {
            throw CouldNotModifyCameraException::forError($exception);
        }
    }
}
