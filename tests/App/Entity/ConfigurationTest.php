<?php
declare(strict_types=1);

namespace App\Tests\App\Entity;

use App\Entity\CameraConfiguration;
use App\Entity\Configuration;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    public function testCameraConfiguration()
    {
        $cameraConfiguration = new CameraConfiguration();
        $configuration = new Configuration();
        $configuration->getCameraConfiguration()->add($cameraConfiguration);
        $this->assertInstanceOf(CameraConfiguration::class, $configuration->getCameraConfiguration()->first());
    }
}
