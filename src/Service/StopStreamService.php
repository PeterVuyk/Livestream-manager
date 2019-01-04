<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\CameraConfiguration;
use App\Exception\InvalidConfigurationsException;
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

    /**
     * @throw InvalidConfigurationsException
     */
    public function process(): void
    {
        if (!$this->statusStreamService->isRunning()) {
            $this->logger->warning('Stream tried to stop while it wasn\'t running');
            return;
        }

        $configurations = $this->getConfigurations();
        if ($configurations->checkIfMixerIsRunning === 'true') {
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

        exec($configurations->stopStreamCommand);
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
     * @throws InvalidConfigurationsException
     * @return \stdClass
     */
    private function getConfigurations()
    {
        $configurations = $this->cameraConfigurationService->getConfigurationsKeyValue();
        try {
            Assert::propertyExists($configurations, CameraConfiguration::MIXER_INTERVAL_TIME);
            Assert::propertyExists($configurations, CameraConfiguration::KEY_MIXER_RETRY_ATTEMPTS);
            Assert::propertyExists($configurations, CameraConfiguration::KEY_CHECK_IF_MIXER_IS_RUNNING);
            Assert::propertyExists($configurations, CameraConfiguration::KEY_MIXER_IP_ADDRESS);
            Assert::propertyExists($configurations, CameraConfiguration::KEY_STOP_STREAM_COMMAND);
        } catch (\InvalidArgumentException $exception) {
            InvalidConfigurationsException::fromError($exception);
        }
        return $configurations;
    }
}
