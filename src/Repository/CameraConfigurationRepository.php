<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\CameraConfiguration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CameraConfigurationRepository extends ServiceEntityRepository
{
    /**
     * CameraConfigurationRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CameraConfiguration::class);
    }

    /**
     * @param CameraConfiguration[] $cameraConfigurations
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function saveFromConfiguration(array $cameraConfigurations)
    {
        foreach ($cameraConfigurations as $cameraConfiguration) {
            $this->getEntityManager()->persist($cameraConfiguration);
        }
        $this->getEntityManager()->flush();
    }
}
