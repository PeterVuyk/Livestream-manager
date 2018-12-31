<?php
declare(strict_types=1);

namespace App\Service;

use Psr\Log\LoggerInterface;

class StopStreamService implements StreamInterface
{
    /** @var CameraConfigurationService */
    private $cameraConfigurationService;

    /** @var StatusStreamService */
    private $statusStreamService;

    /** @var LoggerInterface */
    private $logger;

    /**
     * StopStreamService constructor.
     * @param CameraConfigurationService $cameraConfigurationService
     * @param StatusStreamService $statusStreamService
     * @param LoggerInterface $logger
     */
    public function __construct(
        CameraConfigurationService $cameraConfigurationService,
        StatusStreamService $statusStreamService,
        LoggerInterface $logger
    ) {
        $this->cameraConfigurationService = $cameraConfigurationService;
        $this->statusStreamService = $statusStreamService;
        $this->logger = $logger;
    }

    public function process(): void
    {
        if (!$this->statusStreamService->isRunning()) {
            $this->logger->warning('Stream tried to stop while it wasn\'t running');
            return;
        }

        $configurations = $this->cameraConfigurationService->getConfigurationsKeyValue();
        if ($configurations->checkIfMixerIsRunning) {
            $attempts = 0;
            do {
                if (!$this->isMixerRunning($configurations->mixerIPAddress)) {
                    break;
                }
                $attempts++;
                sleep((int)$configurations->mixerDelayTime);
                $this->logger->info('Stop stream delayed, mixer is still running');
            } while ($attempts <= $configurations->mixerDelayAttempts);
        }

        exec('killall -9 picam');
        $this->logger->info('Livestream is stopped successfully');
    }

    /**
     * @param string $mixerIPAddress
     * @return bool
     */
    private function isMixerRunning(string $mixerIPAddress): bool
    {
        if ($socket =@ fsockopen($mixerIPAddress, 80, $errno, $errstr, 30)) {
            fclose($socket);
            return true;
        }
        return false;
    }
}
