<?php
declare(strict_types=1);

namespace App\Service\StreamProcessing;

use App\Entity\CameraConfiguration;
use App\Exception\CouldNotStartLivestreamException;
use App\Exception\InvalidConfigurationsException;
use App\Repository\CameraRepository;
use App\Service\CameraConfigurationService;
use App\Service\StateMachineInterface;
use Psr\Log\LoggerInterface;
use Webmozart\Assert\Assert;

class StartStreamService implements StreamInterface
{
    /** @var CameraConfigurationService */
    private $cameraConfigurationService;

    /** @var StatusStreamService */
    private $statusStreamService;

    /** @var LoggerInterface */
    private $logger;

    /** @var CameraRepository */
    private $cameraRepository;

    /** @var StateMachineInterface */
    private $streamStateMachine;

    /**
     * StartStreamService constructor.
     * @param CameraConfigurationService $cameraConfigurationService
     * @param StatusStreamService $statusStreamService
     * @param LoggerInterface $logger
     * @param CameraRepository $cameraRepository ;
     * @param StateMachineInterface $streamStateMachine
     */
    public function __construct(
        CameraConfigurationService $cameraConfigurationService,
        StatusStreamService $statusStreamService,
        LoggerInterface $logger,
        cameraRepository $cameraRepository,
        StateMachineInterface $streamStateMachine
    ) {
        $this->cameraConfigurationService = $cameraConfigurationService;
        $this->statusStreamService = $statusStreamService;
        $this->logger = $logger;
        $this->cameraRepository = $cameraRepository;
        $this->streamStateMachine = $streamStateMachine;
    }

    /**
     * @throws InvalidConfigurationsException
     * @throws CouldNotStartLivestreamException
     */
    public function process(): void
    {
        $camera = $this->cameraRepository->getMainCamera();
        $toStarting = $this->streamStateMachine->can($camera, 'to_starting');
        if (!$toStarting || $this->statusStreamService->isRunning()) {
            $this->logger->warning('Stream tried to start while stream was already running');
            return;
        }
        $this->streamStateMachine->apply($camera, 'to_starting');

        $configurations = $this->getConfigurations();
        if (!$this->isHostAvailable($configurations)) {
            throw CouldNotStartLivestreamException::hostNotAvailable();
        }
        $this->logger->info('host is available');

        exec(
            "{$configurations->ffmpegLocationApplication} -i {$configurations->inputCameraAddress} \
            -af \"{$configurations->increaseVolumeInput}\" -c:v copy -ac 1 \
            -ar {$configurations->audioSamplingFrequency} -ab {$configurations->audioBitrate} \
            -map_channel {$configurations->mapAudioChannel} -map_channel {$configurations->mapAudioChannel} \
			-f {$configurations->outputStreamFormat} | {$configurations->cameraLocationApplication} \
			 --alsadev {$configurations->hardwareVideoDevice} --tcpout \
			{$configurations->outputVideoLocation} --hflip --vflip - -r {$configurations->audioSamplingFrequency} \
			-a {$configurations->audioBitrate} --volume {$configurations->audioVolume} \
			--videobitrate {$configurations->videoBitrate}"
        );

        $this->streamStateMachine->apply($camera, 'to_running');
        $this->logger->info('Livestream is streaming');
    }

    /**
     * @param object $configurations
     * @return bool
     */
    private function isHostAvailable(object $configurations): bool
    {
        $attempts = 0;
        do {
            if ($socket =@ fsockopen($configurations->livestreamServer, 80, $errno, $errstr, 30)) {
                fclose($socket);
                return true;
            }
            $attempts++;
            $this->logger->warning("host was not available, attempts: {$attempts}");
            sleep((int)$configurations->intervalIsServerAvailable);
        } while ($attempts <= $configurations->retryIsServerAvailable);
        return false;
    }

    /**
     * @throws \InvalidArgumentException
     * @return \stdClass
     */
    private function getConfigurations()
    {
        try {
            $configurations = $this->cameraConfigurationService->getConfigurationsKeyValue();
            Assert::propertyExists($configurations, CameraConfiguration::KEY_LIVESTREAM_SERVER);
            Assert::propertyExists($configurations, CameraConfiguration::KEY_FFMPEG_LOCATION_APPLICATION);
            Assert::propertyExists($configurations, CameraConfiguration::KEY_INPUT_CAMERA_ADDRESS);
            Assert::propertyExists($configurations, CameraConfiguration::KEY_INCREASE_VOLUME_INPUT);
            Assert::propertyExists($configurations, CameraConfiguration::KEY_AUDIO_BITRATE);
            Assert::propertyExists($configurations, CameraConfiguration::KEY_AUDIO_SAMPLING_FREQUENCY);
            Assert::propertyExists($configurations, CameraConfiguration::KEY_MAP_AUDIO_CHANNEL);
            Assert::propertyExists($configurations, CameraConfiguration::KEY_OUTPUT_STREAM_FORMAT);
            Assert::propertyExists($configurations, CameraConfiguration::KEY_CAMERA_LOCATION_APPLICATION);
            Assert::propertyExists($configurations, CameraConfiguration::KEY_HARDWARE_VIDEO_DEVICE);
            Assert::propertyExists($configurations, CameraConfiguration::KEY_OUTPUT_VIDEO_LOCATION);
            Assert::propertyExists($configurations, CameraConfiguration::KEY_AUDIO_VOLUME);
            Assert::propertyExists($configurations, CameraConfiguration::KEY_VIDEO_BITRATE);
            Assert::propertyExists($configurations, CameraConfiguration::KEY_INTERVAL_IS_SERVER_AVAILABLE);
            Assert::propertyExists($configurations, CameraConfiguration::KEY_RETRY_IS_SERVER_AVAILABLE);
        } catch (\InvalidArgumentException $exception) {
            throw InvalidConfigurationsException::fromError($exception);
        }
        return $configurations;
    }
}
