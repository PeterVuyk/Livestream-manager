<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\CameraConfiguration;
use Psr\Log\LoggerInterface;
use Webmozart\Assert\Assert;

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

        //TODO: Add clause to check if it need to be checked if mixer is online.
        $configurations = $this->getConfigurations();
        if ($configurations->checkIfMixerIsRunning) {
            $attempts = 0;
            do {
                if (!$this->isMixerRunning($configurations->mixerIPAddress)) {
                    break;
                }
                $attempts++;
                sleep((int)$configurations->mixerIntervalTime);
                $this->logger->info('Stop stream delayed, mixer is still running');
            } while ($attempts <= $configurations->mixerRetryAttempts);
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

    /**
     * @throws \InvalidArgumentException
     * @return \stdClass
     */
    private function getConfigurations()
    {
        $configurations = $this->cameraConfigurationService->getConfigurationsKeyValue();
        $requiredProperties = [
            CameraConfiguration::MIXER_INTERVAL_TIME,
            CameraConfiguration::KEY_MIXER_RETRY_ATTEMPTS,
            CameraConfiguration::KEY_CHECK_IF_MIXER_IS_RUNNING,
            CameraConfiguration::KEY_MIXER_IP_ADDRESS,
        ];
        Assert::allPropertyExists($configurations, $requiredProperties);
        return $configurations;
    }
}
