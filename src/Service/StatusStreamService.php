<?php
declare(strict_types=1);

namespace App\Service;

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
     * @return bool
     */
    public function isRunning(): bool
    {
        $configurations = $this->cameraConfigurationService->getConfigurationsKeyValue();
        exec("ps aux | grep \"$configurations->picamLocationApplication\" | grep -v \"grep\" ; echo $?", $output);
        if ($output === self::STATUS_RUNNING) {
            return true;
        }
        return false;
    }
}
