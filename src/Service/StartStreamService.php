<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\CameraConfiguration;
use App\Exception\CouldNotStartLivestreamException;
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

    /**
     * StartStreamService constructor.
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
     * @throws CouldNotStartLivestreamException
     */
    public function process(): void
    {
        if ($this->statusStreamService->isRunning()) {
            $this->logger->warning('Stream tried to start while stream was already running');
            return;
        }

        $configurations = $this->getConfigurations();
        if (!$this->isHostAvailable($configurations)) {
            throw CouldNotStartLivestreamException::hostNotAvailable();
        }

        $this->createCameraDirectories();
        $this->logger->info('Livestream is online');

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

        $this->logger->info('Livestream is streaming');
    }

    /**
     * @throws CouldNotStartLivestreamException
     */
    private function createCameraDirectories()
    {
        if (!file_exists('/run/shm')) {
            if (!mkdir('/run/shm/hooks/') || !mkdir('/run/shm/rec/') || !mkdir('/run/shm/state/')) {
                throw CouldNotStartLivestreamException::couldNotCreateRequiredDirectories();
            }
        }

        if (is_link('/run/shm/rec/archive')) {
            return;
        }

        if (!symlink('/home/pi/picam/archive', '/run/shm/rec/archive')) {
            throw CouldNotStartLivestreamException::couldNotCreateASymlink();
        }
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
            sleep($configurations->intervalIsServerAvailable);
        }
        while ($attempts <= $configurations->retryIsServerAvailable);
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
            CameraConfiguration::KEY_LIVESTREAM_SERVER,
            CameraConfiguration::KEY_INTERVAL_IS_SERVER_AVAILABLE,
            CameraConfiguration::KEY_RETRY_IS_SERVER_AVAILABLE,
            CameraConfiguration::KEY_VIDEO_BITRATE,
            CameraConfiguration::KEY_AUDIO_VOLUME,
            CameraConfiguration::KEY_OUTPUT_VIDEO_LOCATION,
            CameraConfiguration::KEY_HARDWARE_VIDEO_DEVICE,
            CameraConfiguration::KEY_CAMERA_LOCATION_APPLICATION,
            CameraConfiguration::KEY_OUTPUT_STREAM_FORMAT,
            CameraConfiguration::KEY_MAP_AUDIO_CHANNEL,
            CameraConfiguration::KEY_AUDIO_BITRATE,
            CameraConfiguration::KEY_AUDIO_SAMPLING_FREQUENCY,
            CameraConfiguration::KEY_INCREASE_VOLUME_INPUT,
            CameraConfiguration::KEY_INPUT_CAMERA_ADDRESS,
            CameraConfiguration::KEY_FFMPEG_LOCATION_APPLICATION,
        ];
        Assert::allPropertyExists($configurations, $requiredProperties);
        return $configurations;
    }
}
