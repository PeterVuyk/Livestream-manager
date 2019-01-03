<?php
declare(strict_types=1);

namespace App\Tests\App\Service;

use App\Entity\CameraConfiguration;
use App\Service\CameraConfigurationService;
use App\Service\StatusStreamService;
use App\Service\StopStreamService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @coversDefaultClass \App\Service\StopStreamService
 * @covers ::<!public>
 * @covers ::__construct()
 */
class StopStreamServiceTest extends TestCase
{
    /** @var LoggerInterface|MockObject */
    private $loggerMock;

    /** @var CameraConfigurationService|MockObject */
    private $cameraConfigurationServiceMock;

    /** @var StatusStreamService|MockObject */
    private $statusStreamServiceMock;

    /** @var StopStreamService */
    private $stopStreamService;

    public function setUp()
    {
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->cameraConfigurationServiceMock = $this->createMock(CameraConfigurationService::class);
        $this->statusStreamServiceMock = $this->createMock(StatusStreamService::class);
        $this->stopStreamService = new StopStreamService(
            $this->cameraConfigurationServiceMock,
            $this->statusStreamServiceMock,
            $this->loggerMock
        );
    }

    /**
     * @covers ::process
     */
    public function testProcessSuccessNoMixerCheck()
    {
        $configurations = [
            CameraConfiguration::KEY_STOP_STREAM_COMMAND => 'echo stop',
            CameraConfiguration::MIXER_INTERVAL_TIME => 0,
            CameraConfiguration::KEY_MIXER_RETRY_ATTEMPTS => 3,
            CameraConfiguration::KEY_CHECK_IF_MIXER_IS_RUNNING => 'false',
            CameraConfiguration::KEY_MIXER_IP_ADDRESS => '123.456.789.012',
        ];

        $this->cameraConfigurationServiceMock->expects($this->once())
            ->method('getConfigurationsKeyValue')
            ->willReturn((object)$configurations);

        $this->loggerMock->expects($this->once())->method('info');
        $this->statusStreamServiceMock->expects($this->once())->method('isRunning')->willReturn(true);

        $this->stopStreamService->process();
        $this->addToAssertionCount(1);
    }

    /**
     * @covers ::process
     */
    public function testProcessSuccessCheckIfMixerIsRunning()
    {
        $configurations = [
            CameraConfiguration::KEY_STOP_STREAM_COMMAND => 'echo stop',
            CameraConfiguration::MIXER_INTERVAL_TIME => 0,
            CameraConfiguration::KEY_MIXER_RETRY_ATTEMPTS => 3,
            CameraConfiguration::KEY_CHECK_IF_MIXER_IS_RUNNING => 'true',
            CameraConfiguration::KEY_MIXER_IP_ADDRESS => '127.0.0.1',
        ];

        $this->cameraConfigurationServiceMock->expects($this->once())
            ->method('getConfigurationsKeyValue')
            ->willReturn((object)$configurations);

        $this->loggerMock->expects($this->atLeastOnce())->method('info');
        $this->statusStreamServiceMock->expects($this->once())->method('isRunning')->willReturn(true);

        $this->stopStreamService->process();
        $this->addToAssertionCount(1);
    }

    /**
     * @covers ::process
     */
    public function testProcessSuccessMixerNotAvailable()
    {
        $configurations = [
            CameraConfiguration::KEY_STOP_STREAM_COMMAND => 'echo stop',
            CameraConfiguration::MIXER_INTERVAL_TIME => 0,
            CameraConfiguration::KEY_MIXER_RETRY_ATTEMPTS => 3,
            CameraConfiguration::KEY_CHECK_IF_MIXER_IS_RUNNING => 'true',
            CameraConfiguration::KEY_MIXER_IP_ADDRESS => '123.456.789.123',
        ];

        $this->cameraConfigurationServiceMock->expects($this->once())
            ->method('getConfigurationsKeyValue')
            ->willReturn((object)$configurations);

        $this->loggerMock->expects($this->atLeastOnce())->method('info');
        $this->statusStreamServiceMock->expects($this->once())->method('isRunning')->willReturn(true);

        $this->stopStreamService->process();
        $this->addToAssertionCount(1);
    }

    /**
     * @covers ::process
     */
    public function testProcessStreamNotRunning()
    {
        $this->loggerMock->expects($this->once())->method('warning');
        $this->statusStreamServiceMock->expects($this->once())->method('isRunning')->willReturn(false);

        $this->stopStreamService->process();
        $this->addToAssertionCount(1);
    }
}
