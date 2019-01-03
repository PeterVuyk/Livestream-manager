<?php
declare(strict_types=1);

namespace App\Tests\App\Service;

use App\Entity\CameraConfiguration;
use App\Service\CameraConfigurationService;
use App\Service\StatusStreamService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Service\StatusStreamService
 * @covers ::<!public>
 * @covers ::__construct()
 * @uses \App\Entity\CameraConfiguration
 * @uses \App\Service\StatusStreamService
 */
class StatusStreamServiceTest extends TestCase
{
    /** @var CameraConfigurationService|MockObject */
    private $cameraConfigurationServiceMock;

    /** @var StatusStreamService */
    private $statusStreamService;

    public function setUp()
    {
        $this->cameraConfigurationServiceMock = $this->createMock(CameraConfigurationService::class);
        $this->statusStreamService = new StatusStreamService($this->cameraConfigurationServiceMock);
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

        $isRunning = $this->statusStreamService->isRunning();
        $this->assertFalse($isRunning);
    }
}
