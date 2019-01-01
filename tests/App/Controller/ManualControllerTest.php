<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use App\Controller\ManualController;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass \App\Controller\ManualController
 * @covers ::<!public>
 * @covers ::__construct
 */
class ManualControllerTest extends TestCase
{
    /** @var ManualController */
    private $manualController;

    /** @var \Twig_Environment|MockObject */
    private $twig;

    public function setUp()
    {
        $this->twig = $this->createMock(\Twig_Environment::class);
        $this->manualController = new ManualController($this->twig);
    }

    /**
     * @covers ::manualPage
     */
    public function testManualPage()
    {
        $this->twig->expects($this->once())->method('render')->willReturn('<p>hi</p>');
        $response = $this->manualController->manualPage();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }
}
