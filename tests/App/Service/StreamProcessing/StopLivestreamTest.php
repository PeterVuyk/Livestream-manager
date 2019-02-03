<?php
declare(strict_types=1);

namespace App\Tests\Service\StreamProcessing;

use App\Entity\Camera;
use App\Entity\CameraConfiguration;
use App\Exception\Livestream\CouldNotStopLivestreamException;
use App\Repository\CameraRepository;
use App\Service\CameraConfigurationService;
use App\Service\StreamProcessing\StatusLivestream;
use App\Service\StreamProcessing\StopLivestream;
use App\Service\StreamProcessing\StreamStateMachine;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @coversDefaultClass \App\Service\StreamProcessing\StopLivestream
 * @covers ::<!public>
 * @covers ::__construct()
 */
class StopLivestreamTest extends TestCase
{
    /** @var LoggerInterface|MockObject */
    private $loggerMock;

    /** @var CameraConfigurationService|MockObject */
    private $cameraConfigurationServiceMock;

    /** @var StatusLivestream|MockObject */
    private $statusLivestreamMock;

    /** @var StreamStateMachine|MockObject */
    private $streamStateMachineMock;

    /** @var CameraRepository|MockObject */
    private $cameraRepositoryMock;

    /** @var StopLivestream */
    private $stopLivestream;

    public function setUp()
    {
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->cameraConfigurationServiceMock = $this->createMock(CameraConfigurationService::class);
        $this->statusLivestreamMock = $this->createMock(StatusLivestream::class);
        $this->streamStateMachineMock = $this->createMock(StreamStateMachine::class);
        $this->cameraRepositoryMock = $this->createMock(CameraRepository::class);
        $this->stopLivestream = new StopLivestream(
            $this->cameraConfigurationServiceMock,
            $this->statusLivestreamMock,
            $this->loggerMock,
            $this->streamStateMachineMock,
            $this->cameraRepositoryMock
        );
    }

    /**
     * @covers ::process
     */
    public function testProcessSuccessNoMixerCheck()
    {
        $configurations = [
            CameraConfiguration::KEY_STOP_STREAM_COMMAND => 'ls',
            CameraConfiguration::KEY_MIXER_INTERVAL_TIME => 0,
            CameraConfiguration::KEY_MIXER_RETRY_ATTEMPTS => 3,
            CameraConfiguration::KEY_CHECK_IF_MIXER_IS_RUNNING => 'false',
            CameraConfiguration::KEY_MIXER_IP_ADDRESS => '123.456.789.012',
        ];

        $this->cameraConfigurationServiceMock->expects($this->once())
            ->method('getConfigurationsKeyValue')
            ->willReturn((object)$configurations);

        $this->statusLivestreamMock->expects($this->once())->method('isRunning')->willReturn(true);

        $this->cameraRepositoryMock->expects($this->once())->method('getMainCamera')->willReturn(new Camera());
        $this->streamStateMachineMock->expects($this->once())->method('can')->willReturn(true);

        $this->stopLivestream->process();
        $this->addToAssertionCount(1);
    }

    /**
     * @covers ::process
     */
    public function testProcessSuccessCheckIfMixerIsRunning()
    {
        $configurations = [
            CameraConfiguration::KEY_STOP_STREAM_COMMAND => 'ls',
            CameraConfiguration::KEY_MIXER_INTERVAL_TIME => 0,
            CameraConfiguration::KEY_MIXER_RETRY_ATTEMPTS => 3,
            CameraConfiguration::KEY_CHECK_IF_MIXER_IS_RUNNING => 'true',
            CameraConfiguration::KEY_MIXER_IP_ADDRESS => '127.0.0.1',
        ];

        $this->cameraConfigurationServiceMock->expects($this->once())
            ->method('getConfigurationsKeyValue')
            ->willReturn((object)$configurations);

        $this->loggerMock->expects($this->atLeastOnce())->method('info');
        $this->statusLivestreamMock->expects($this->once())->method('isRunning')->willReturn(true);

        $this->cameraRepositoryMock->expects($this->once())->method('getMainCamera')->willReturn(new Camera());
        $this->streamStateMachineMock->expects($this->once())->method('can')->willReturn(true);

        $this->stopLivestream->process();
        $this->addToAssertionCount(1);
    }

    /**
     * @covers ::process
     */
    public function testProcessSuccessMixerNotAvailable()
    {
        $configurations = [
            CameraConfiguration::KEY_STOP_STREAM_COMMAND => 'ls',
            CameraConfiguration::KEY_MIXER_INTERVAL_TIME => 0,
            CameraConfiguration::KEY_MIXER_RETRY_ATTEMPTS => 3,
            CameraConfiguration::KEY_CHECK_IF_MIXER_IS_RUNNING => 'true',
            CameraConfiguration::KEY_MIXER_IP_ADDRESS => '123.456.789.123',
        ];

        $this->cameraConfigurationServiceMock->expects($this->once())
            ->method('getConfigurationsKeyValue')
            ->willReturn((object)$configurations);

        $this->statusLivestreamMock->expects($this->once())->method('isRunning')->willReturn(true);

        $this->cameraRepositoryMock->expects($this->once())->method('getMainCamera')->willReturn(new Camera());
        $this->streamStateMachineMock->expects($this->once())->method('can')->willReturn(true);

        $this->stopLivestream->process();
        $this->addToAssertionCount(1);
    }

    /**
     * @covers ::process
     */
    public function testProcessStreamNotRunning()
    {
        $this->expectException(CouldNotStopLivestreamException::class);
        $this->statusLivestreamMock->expects($this->once())->method('isRunning')->willReturn(false);

        $this->cameraRepositoryMock->expects($this->once())->method('getMainCamera')->willReturn(new Camera());
        $this->streamStateMachineMock->expects($this->once())->method('can')->willReturn(true);

        $this->stopLivestream->process();
    }
}
