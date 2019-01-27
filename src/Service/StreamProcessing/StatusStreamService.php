<?php
declare(strict_types=1);

namespace App\Service\StreamProcessing;

use App\Entity\CameraConfiguration;
use App\Exception\InvalidConfigurationsException;
use App\Service\CameraConfigurationService;
use Webmozart\Assert\Assert;

class StatusStreamService
{
    const STATUS_RUNNING = 0;

    /** @var CameraConfigurationService */
    private $cameraConfigurationService;

    /**
     * StatusStreamService constructor.
     * @param CameraConfigurationService $cameraConfigurationService
     */
    public function __construct(
        CameraConfigurationService $cameraConfigurationService
    ) {
        $this->cameraConfigurationService = $cameraConfigurationService;
    }

    /**
     * @throws InvalidConfigurationsException
     * @return bool
     */
    public function isRunning(): bool
    {
        $cameraLocationApplication = $this->getConfigurations()->cameraLocationApplication;
        exec("ps aux | grep \"{$cameraLocationApplication}\" | grep -v \"grep\" ; echo $?", $output);
        if ($output === self::STATUS_RUNNING) {
            return true;
        }
        return false;
    }

    /**
     * @throws InvalidConfigurationsException
     * @return \stdClass
     */
    private function getConfigurations()
    {
        $configurations = $this->cameraConfigurationService->getConfigurationsKeyValue();
        try {
            Assert::propertyExists($configurations, CameraConfiguration::KEY_CAMERA_LOCATION_APPLICATION);
        } catch (\InvalidArgumentException $exception) {
            throw InvalidConfigurationsException::fromError($exception);
        }
        return $configurations;
    }
}
