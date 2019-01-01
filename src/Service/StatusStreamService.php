<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\CameraConfiguration;
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
    public function __construct(CameraConfigurationService $cameraConfigurationService)
    {
        $this->cameraConfigurationService = $cameraConfigurationService;
    }

    /**
     * @throws \InvalidArgumentException
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
     * @throws \InvalidArgumentException
     * @return \stdClass
     */
    private function getConfigurations()
    {
        $configurations = $this->cameraConfigurationService->getConfigurationsKeyValue();
        Assert::propertyExists($configurations, CameraConfiguration::KEY_CAMERA_LOCATION_APPLICATION);
        return $configurations;
    }
}
