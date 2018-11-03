<?php
declare(strict_types=1);

namespace App\Tests\App\Controller;

use App\Controller\HomepageController;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class HomepageControllerTest extends TestCase
{
    /** @var HomepageController */
    private $homepageController;

    /** @var \Twig_Environment|MockObject */
    private $twig;

    public function setUp()
    {
        $this->twig = $this->createMock(\Twig_Environment::class);
        $this->homepageController = new HomepageController($this->twig);
    }

    public function testHome()
    {
        $this->twig->expects($this->once())->method('render')->willReturn('<p>hi</p>');
        $result = $this->homepageController->home();
        $this->assertSame(Response::HTTP_OK, $result->getStatusCode());
    }

    public function testAdmin()
    {
        $this->twig->expects($this->once())->method('render')->willReturn('<p>hi</p>');
        $result = $this->homepageController->admin();
        $this->assertSame(Response::HTTP_OK, $result->getStatusCode());
    }
}
