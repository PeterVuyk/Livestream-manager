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

        if (!$this->isHostAvailable()) {
            throw CouldNotStartLivestreamException::hostNotAvailable();
        }

        $this->createPicamDirectories();

        $this->logger->info('Livestream is online');

        exec(
            "/usr/local/bin/ffmpeg -i tcp://127.0.0.1:8181?listen \
            -af \"volume=24dB\" -c:v copy -ac 1 -ar 44100 -ab 192000 -map_channel 0.1.0 -map_channel 0.1.0 \
			-f flv rtmp://live.dedeurzwolle.nl:1935/live/dienst | /home/pi/picam/picam --alsadev hw:1,0 --tcpout \
			tcp://127.0.0.1:8181 --hflip --vflip - -r 44100 \
			-a 192000 --volume 1.0 --videobitrate 1500000"
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
        if (!symlink('/home/pi/picam/archive', '/run/shm/rec/archive')) {
            throw CouldNotStartLivestreamException::couldNotCreateASymlink();
        }
    }

    /**
     * @return bool
     */
    private function isHostAvailable(): bool
    {
        $configurations = $this->cameraConfigurationService->getConfigurationsKeyValue();
        $attempts = 0;
        do {
            if ($socket =@ fsockopen('live.dedeurzwolle.nl', 80, $errno, $errstr, 30)) {
                fclose($socket);
                return true;
            }
            $this->logger->warning("host was not available, attempts: {$attempts}");
            $attempts++;
            sleep($configurations->sleepTime);
        }
        while ($attempts <= $configurations->retry);
        return false;
    }
}
