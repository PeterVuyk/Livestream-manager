<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\CameraConfiguration;
use App\Exception\CouldNotModifyCameraConfigurationException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
     * @throws CouldNotModifyCameraConfigurationException
     */
    public function saveFromConfiguration(array $cameraConfigurations)
    {
        try {
            foreach ($cameraConfigurations as $cameraConfiguration) {
                $this->getEntityManager()->persist($cameraConfiguration);
            }
            $this->getEntityManager()->flush();
        } catch (ORMException $exception) {
            throw CouldNotModifyCameraConfigurationException::forError($exception);
        }
    }
}
