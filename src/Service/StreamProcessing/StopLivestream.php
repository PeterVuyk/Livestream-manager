<?php
declare(strict_types=1);

namespace App\Service\StreamProcessing;

use App\Entity\CameraConfiguration;
use App\Entity\StateAwareInterface;
use App\Exception\Repository\CouldNotModifyCameraException;
use App\Exception\Livestream\CouldNotStopLivestreamException;
use App\Exception\Livestream\InvalidConfigurationsException;
use App\Repository\CameraRepository;
use App\Service\CameraConfigurationService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Process;
use Webmozart\Assert\Assert;

class StopLivestream implements LivestreamInterface
{
    /** @var CameraConfigurationService */
    private $cameraConfigurationService;

    /** @var StatusLivestream */
    private $statusLivestream;

    /** @var LoggerInterface */
    private $logger;

    /** @var StreamStateMachine */
    private $streamStateMachine;

    /** @var CameraRepository */
    private $cameraRepository;

    /**
     * @param CameraConfigurationService $cameraConfigurationService
     * @param StatusLivestream $statusLivestream
     * @param LoggerInterface $logger
     * @param StreamStateMachine $streamStateMachine
     * @param CameraRepository $cameraRepository
     */
    public function __construct(
        CameraConfigurationService $cameraConfigurationService,
        StatusLivestream $statusLivestream,
        LoggerInterface $logger,
        StreamStateMachine $streamStateMachine,
        CameraRepository $cameraRepository
    ) {
        $this->cameraConfigurationService = $cameraConfigurationService;
        $this->statusLivestream = $statusLivestream;
        $this->logger = $logger;
        $this->streamStateMachine = $streamStateMachine;
        $this->cameraRepository = $cameraRepository;
    }

    /**
     * @throw InvalidConfigurationsException
     * @throws CouldNotModifyCameraException
     * @throws CouldNotStopLivestreamException
     */
    public function process(): void
    {
        $camera = $this->cameraRepository->getMainCamera();
        $toStopping = $this->streamStateMachine->can($camera, 'to_stopping');
        $cameraStreaming = $this->statusLivestream->isRunning();

        if (!$toStopping || !$cameraStreaming) {
            throw CouldNotStopLivestreamException::invalidStateOrCameraStatus($toStopping, $cameraStreaming);
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
        $process->setTimeout(120);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->streamStateMachine->apply($camera, 'to_failure');
            throw CouldNotStopLivestreamException::runProcessFailed($process->getErrorOutput());
        }

        $this->streamStateMachine->apply($camera, 'to_inactive');
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
