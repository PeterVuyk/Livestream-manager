<?php
declare(strict_types=1);

namespace App\Tests\App\Entity;

use App\Entity\CameraConfiguration;
use PHPUnit\Framework\TestCase;

class CameraConfigurationTest extends TestCase
{
    public function testKey()
    {
        $cameraConfiguration = new CameraConfiguration();
        $cameraConfiguration->setKey('key!');
        $this->assertSame('key!', $cameraConfiguration->getKey());
    }

    public function testValue()
    {
        $cameraConfiguration = new CameraConfiguration();
        $cameraConfiguration->setValue('value123');
        $this->assertSame('value123', $cameraConfiguration->getValue());
    }
}
