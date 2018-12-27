<?php
declare(strict_types=1);

namespace App\Tests\App\Controller;

use App\Controller\Controller;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class ControllerTest extends TestCase
{
    /** @var \Twig_Environment|MockObject */
    private $twig;

    /** @var TestController */
    private $controller;

    public function setUp()
    {
        $this->twig = $this->createMock(\Twig_Environment::class);
        $this->controller = new TestController($this->twig);
    }

    public function testRenderSuccess()
    {
        $this->twig->expects($this->once())->method('render')->willReturn('<p>hi</p>');
        $response = $this->controller->getRender('some-name', ['array' => 'value', Response::HTTP_OK]);
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSame('<p>hi</p>', $response->getContent());
    }

    public function testRenderFailed()
    {
        $this->twig->expects($this->once())->method('render')->willThrowException(new \Exception('oeps'));
        $response = $this->controller->getRender('some-name', ['array' => 'value', Response::HTTP_OK]);
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSame('oeps', $response->getContent());
    }
}

class TestController extends Controller
{
    public function getRender($name, array $parameters = [], Response $response = null)
    {
        return $this->render($name, $parameters = [], $response = null);
    }
}
