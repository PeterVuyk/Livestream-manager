<?php
declare(strict_types=1);

namespace App\Tests\Service\StreamProcessing;

use App\Entity\CameraConfiguration;
use App\Service\CameraConfigurationService;
use App\Service\StreamProcessing\StatusLivestream;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Service\StreamProcessing\StatusLivestream
 * @covers ::<!public>
 * @covers ::__construct()
 * @uses \App\Entity\CameraConfiguration
 * @uses \App\Service\StreamProcessing\StatusLivestream
 */
class StatusLivestreamTest extends TestCase
{
    /** @var CameraConfigurationService|MockObject */
    private $cameraConfigurationServiceMock;

    /** @var StatusLivestream */
    private $statusLivestream;

    public function setUp()
    {
        $this->cameraConfigurationServiceMock = $this->createMock(CameraConfigurationService::class);
        $this->statusLivestream = new StatusLivestream($this->cameraConfigurationServiceMock);
    }

    /**
     * @covers ::isRunning
     */
    public function testIsRunning()
    {
        $configurations = [CameraConfiguration::KEY_CAMERA_LOCATION_APPLICATION => 'cat'];
        $this->cameraConfigurationServiceMock->expects($this->once())
            ->method('getConfigurationsKeyValue')
            ->willReturn((object)$configurations);

        $isRunning = $this->statusLivestream->isRunning();
        $this->assertFalse($isRunning);
    }
}
