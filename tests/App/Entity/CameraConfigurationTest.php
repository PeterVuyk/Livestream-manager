<?php
declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\CameraConfiguration;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Entity\CameraConfiguration
 * @covers ::<!public>
 * @uses \App\Entity\CameraConfiguration
 */
class CameraConfigurationTest extends TestCase
{
    /**
     * @covers ::setKey
     * @covers ::getKey
     */
    public function testKey()
    {
        $cameraConfiguration = new CameraConfiguration();
        $cameraConfiguration->setKey('key!');
        $this->assertSame('key!', $cameraConfiguration->getKey());
    }

    /**
     * @covers ::setValue
     * @covers ::getValue
     */
    public function testValue()
    {
        $cameraConfiguration = new CameraConfiguration();
        $cameraConfiguration->setValue('value123');
        $this->assertSame('value123', $cameraConfiguration->getValue());
    }
}
