<?php
declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Camera;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Entity\Camera
 * @covers ::<!public>
 */
class CameraTest extends TestCase
{
    /**
     * @covers ::setCamera
     * @covers ::getCamera
     */
    public function testCamera()
    {
        $camera = new Camera();
        $camera->setCamera('some-name');
        $this->assertSame('some-name', $camera->getCamera());
    }

    /**
     * @covers ::setState
     * @covers ::getState
     */
    public function testState()
    {
        $camera = new Camera();
        $camera->setState('state');
        $this->assertSame('state', $camera->getState());
    }
}
