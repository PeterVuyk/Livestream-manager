<?php
declare(strict_types=1);

namespace App\Service;

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

    public function process(): void
    {
        $this->cameraConfigurationService->getAllConfigurations();

        if (!$this->isHostAvailable()) {
            throw new \Exception();
        }

        if ($this->statusStreamService->isRunning()) {
            $this->logger->warning('Stream tried to start while stream was already running');
            return;
        }

        if (!file_exists('/run/shm')) {
            if (!mkdir('/run/shm/hooks/') || !mkdir('/run/shm/rec/') || !mkdir('/run/shm/state/')) {
                throw new \Exception();
            }
        }
        if (!symlink('/home/pi/picam/archive', '/run/shm/rec/archive')) {
            throw new \Exception();
        };


        $execution = shell_exec(
            "/usr/local/bin/ffmpeg -i tcp://127.0.0.1:8181?listen \
            -af \"volume=24dB\" -c:v copy -ac 1 -ar 44100 -ab 192000 -map_channel 0.1.0 -map_channel 0.1.0 \
			-f flv rtmp://live.dedeurzwolle.nl:1935/live/dienst | /home/pi/picam/picam --alsadev hw:1,0 --tcpout \
			tcp://127.0.0.1:8181 --hflip --vflip - -r 44100 \
			-a 192000 --volume 1.0 --videobitrate 1500000"
        );
        if ($execution === null) {
            throw new \Exception();
        }
    }

    private function isHostAvailable(): bool
    {
        if ($socket =@ fsockopen('live.dedeurzwolle.nl', 80, $errno, $errstr, 30)) {
            fclose($socket);
            return true;
        }
        return false;
    }

    /**
     * @return object \stdClass
     */
    private function getConfigurations()
    {
        $configurations = $this->cameraConfigurationService->getAllConfigurations();

        $result = new \stdClass();
        foreach ($configurations as $configuration) {
            $key = $configuration->getKey();
            $result->$key = $configuration->getValue();
        }
        return $result;
    }
}
