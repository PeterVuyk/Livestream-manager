<?php
declare(strict_types=1);

namespace App\Service;

use App\Exception\CouldNotStartLivestreamException;
use Psr\Log\LoggerInterface;

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

        $configurations = $this->cameraConfigurationService->getConfigurationsKeyValue();
        if (!$this->isHostAvailable($configurations)) {
            throw CouldNotStartLivestreamException::hostNotAvailable();
        }

        $this->createPicamDirectories();
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
    private function createPicamDirectories()
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
}
