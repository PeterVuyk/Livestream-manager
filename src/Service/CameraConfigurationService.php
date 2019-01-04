<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\CameraConfiguration;
use App\Entity\Configuration;
use App\Exception\CouldNotModifyCameraConfigurationException;
use App\Repository\CameraConfigurationRepository;
use Psr\Log\LoggerInterface;

class CameraConfigurationService
{
    /** @var CameraConfigurationRepository */
    private $cameraConfigurationRepository;

    /** @var LoggerInterface */
    private $logger;

    /**
     * CameraConfigurationService constructor.
     * @param CameraConfigurationRepository $cameraConfigurationRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        CameraConfigurationRepository $cameraConfigurationRepository,
        LoggerInterface $logger
    ) {
        $this->cameraConfigurationRepository = $cameraConfigurationRepository;
        $this->logger = $logger;
    }

    /**
     * @return CameraConfiguration[]
     */
    public function getAllConfigurations()
    {
        return $this->cameraConfigurationRepository->findAll();
    }

    /**
     * @param Configuration $configuration
     * @throws CouldNotModifyCameraConfigurationException
     */
    public function saveConfigurations(Configuration $configuration)
    {
        $this->cameraConfigurationRepository->saveFromConfiguration(
            $configuration->getCameraConfiguration()->toArray()
        );
    }

    /**
     * @return object \stdClass
     */
    public function getConfigurationsKeyValue(): \stdClass
    {
        $configurations = $this->getAllConfigurations();

        $result = new \stdClass();
        foreach ($configurations as $configuration) {
            $key = $configuration->getKey();
            $result->$key = $configuration->getValue();
        }
        return $result;
    }
}
