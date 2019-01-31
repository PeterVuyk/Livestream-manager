<?php
declare(strict_types=1);

namespace App\Tests\Service\StreamProcessing;

use App\Entity\Camera;
use App\Entity\CameraConfiguration;
use App\Exception\CouldNotStartLivestreamException;
use App\Repository\CameraRepository;
use App\Service\CameraConfigurationService;
use App\Service\StateMachineInterface;
use App\Service\StreamProcessing\StartLivestream;
use App\Service\StreamProcessing\StatusLivestream;
use App\Service\StreamProcessing\StreamStateMachine;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @coversDefaultClass \App\Service\StreamProcessing\StartLivestream
 * @covers ::<!public>
 * @covers ::__construct()
 * @uses \App\Entity\CameraConfiguration
 * @uses \App\Service\StreamProcessing\StatusLivestream
 */
class StartLivestreamTest extends TestCase
{
    const TMP_LOCATION = '/tmp/some-location';

    /** @var CameraConfigurationService|MockObject */
    private $cameraConfigurationServiceMock;

    /** @var StatusLivestream|MockObject */
    private $statusLivestreamMock;

    /** @var LoggerInterface|MockObject */
    private $loggerMock;

    /** @var StartLivestream */
    private $startLivestream;

    /** @var CameraRepository|MockObject */
    private $cameraRepositoryMock;

    /** @var StateMachineInterface|MockObject */
    private $streamStateMachineMock;

    public function setUp()
    {
        $this->cameraConfigurationServiceMock = $this->createMock(CameraConfigurationService::class);
        $this->statusLivestreamMock = $this->createMock(StatusLivestream::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->cameraRepositoryMock = $this->createMock(CameraRepository::class);
        $this->streamStateMachineMock = $this->createMock(StreamStateMachine::class);
        $this->startLivestream = new StartLivestream(
            $this->cameraConfigurationServiceMock,
            $this->statusLivestreamMock,
            $this->loggerMock,
            $this->cameraRepositoryMock,
            $this->streamStateMachineMock
        );
    }

    /**
     * @throws CouldNotStartLivestreamException
     * @covers ::process
     */
    public function testProcessStreamAlreadyRunning()
    {
        $this->expectException(CouldNotStartLivestreamException::class);
        $this->statusLivestreamMock->expects($this->once())->method('isRunning')->willReturn(true);
        $this->cameraRepositoryMock->expects($this->once())->method('getMainCamera')->willReturn(new Camera());
        $this->streamStateMachineMock->expects($this->once())->method('can')->willReturn(true);
        $this->startLivestream->process();
    }

    /**
     * @throws CouldNotStartLivestreamException
     * @covers ::process
     */
    public function testProcessHostNotAvailable()
    {
        $this->expectException(CouldNotStartLivestreamException::class);

        $this->loggerMock->expects($this->atLeastOnce())->method('warning');
        $this->cameraConfigurationServiceMock->expects($this->once())
            ->method('getConfigurationsKeyValue')
            ->willReturn($this->getConfigurations());
        $this->cameraRepositoryMock->expects($this->once())->method('getMainCamera')->willReturn(new Camera());
        $this->streamStateMachineMock->expects($this->once())->method('can')->willReturn(true);

        $this->startLivestream->process();
    }

    private function getConfigurations()
    {
        $configurations = [
            CameraConfiguration::KEY_LIVESTREAM_SERVER => 'asdfsfasdfasdfa.asfasfasfa.com',
            CameraConfiguration::KEY_FFMPEG_LOCATION_APPLICATION => self::TMP_LOCATION,
            CameraConfiguration::KEY_INPUT_CAMERA_ADDRESS => 'tcp://127.0.0.1:8181?listen',
            CameraConfiguration::KEY_INCREASE_VOLUME_INPUT => 'volume=24dB',
            CameraConfiguration::KEY_AUDIO_BITRATE => '192000',
            CameraConfiguration::KEY_AUDIO_SAMPLING_FREQUENCY => '44100',
            CameraConfiguration::KEY_MAP_AUDIO_CHANNEL => '0.1.0',
            CameraConfiguration::KEY_OUTPUT_STREAM_FORMAT => 'flv rtmp://live.example.com:1935/given/url',
            CameraConfiguration::KEY_CAMERA_LOCATION_APPLICATION => self::TMP_LOCATION,
            CameraConfiguration::KEY_HARDWARE_VIDEO_DEVICE => 'hw:1,0',
            CameraConfiguration::KEY_OUTPUT_VIDEO_LOCATION => 'tcp://127.0.0.1:8181',
            CameraConfiguration::KEY_AUDIO_VOLUME => '1.0',
            CameraConfiguration::KEY_VIDEO_BITRATE => '1500000',
            CameraConfiguration::KEY_INTERVAL_IS_SERVER_AVAILABLE => '0',
            CameraConfiguration::KEY_RETRY_IS_SERVER_AVAILABLE => '5',
        ];
        return (object)$configurations;
    }

}
