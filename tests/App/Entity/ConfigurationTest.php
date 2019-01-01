<?php
declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\CameraConfiguration;
use App\Entity\Configuration;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Entity\Configuration
 * @covers ::<!public>
 * @covers ::__construct
 * @uses \App\Entity\CameraConfiguration
 */
class ConfigurationTest extends TestCase
{
    /**
     * @covers ::getCameraConfiguration
     */
    public function testCameraConfiguration()
    {
        $cameraConfiguration = new CameraConfiguration();
        $configuration = new Configuration([$cameraConfiguration]);
        $configuration->getCameraConfiguration()->add($cameraConfiguration);
        $this->assertInstanceOf(CameraConfiguration::class, $configuration->getCameraConfiguration()->first());
    }
}
