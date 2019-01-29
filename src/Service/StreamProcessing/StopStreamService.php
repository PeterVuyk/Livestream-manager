<?php
declare(strict_types=1);

namespace App\Service\StreamProcessing;

use App\Entity\CameraConfiguration;
use App\Entity\StateAwareInterface;
use App\Exception\CouldNotModifyCameraException;
use App\Exception\InvalidConfigurationsException;
use App\Repository\CameraRepository;
use App\Service\CameraConfigurationService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Process;
use Webmozart\Assert\Assert;

class StopStreamService implements StreamInterface
{
    /** @var CameraConfigurationService */
    private $cameraConfigurationService;

    /** @var StatusStreamService */
    private $statusStreamService;

    /** @var LoggerInterface */
    private $logger;

    /** @var StreamStateMachine */
    private $streamStateMachine;

    /** @var CameraRepository */
    private $cameraRepository;

    /**
     * StopStreamService constructor.
     * @param CameraConfigurationService $cameraConfigurationService
     * @param StatusStreamService $statusStreamService
     * @param LoggerInterface $logger
     * @param StreamStateMachine $streamStateMachine
     * @param CameraRepository $cameraRepository
     */
    public function __construct(
        CameraConfigurationService $cameraConfigurationService,
        StatusStreamService $statusStreamService,
        LoggerInterface $logger,
        StreamStateMachine $streamStateMachine,
        CameraRepository $cameraRepository
    ) {
        $this->cameraConfigurationService = $cameraConfigurationService;
        $this->statusStreamService = $statusStreamService;
        $this->logger = $logger;
        $this->streamStateMachine = $streamStateMachine;
        $this->cameraRepository = $cameraRepository;
    }

    /**
     * @throw InvalidConfigurationsException
     * @throws CouldNotModifyCameraException
     */
    public function process(): void
    {
        $camera = $this->cameraRepository->getMainCamera();
        $toStopping = $this->streamStateMachine->can($camera, 'to_stopping');
        if (!$toStopping || !$this->statusStreamService->isRunning()) {
            $this->logger->warning('Stream tried to stop while it wasn\'t running');
            return;
        }
        $this->streamStateMachine->apply($camera, 'to_stopping');

        $configurations = $this->getConfigurations($camera);
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

        $process = new Process([$configurations->stopStreamCommand]);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->logger->error('Stop livestream not successful', ['errorOutput' => $process->getErrorOutput()]);
            $this->streamStateMachine->apply($camera, 'to_failure');
            return;
        }

        $this->streamStateMachine->apply($camera, 'to_inactive');
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
     * @param StateAwareInterface $camera
     * @return \stdClass
     * @throws CouldNotModifyCameraException
     */
    private function getConfigurations(StateAwareInterface $camera)
    {
        $configurations = $this->cameraConfigurationService->getConfigurationsKeyValue();
        try {
            Assert::propertyExists($configurations, CameraConfiguration::KEY_MIXER_INTERVAL_TIME);
            Assert::propertyExists($configurations, CameraConfiguration::KEY_MIXER_RETRY_ATTEMPTS);
            Assert::propertyExists($configurations, CameraConfiguration::KEY_CHECK_IF_MIXER_IS_RUNNING);
            Assert::propertyExists($configurations, CameraConfiguration::KEY_MIXER_IP_ADDRESS);
            Assert::propertyExists($configurations, CameraConfiguration::KEY_STOP_STREAM_COMMAND);
        } catch (\InvalidArgumentException $exception) {
            $this->streamStateMachine->apply($camera, 'to_failure');
            InvalidConfigurationsException::fromError($exception);
        }
        return $configurations;
    }
}
