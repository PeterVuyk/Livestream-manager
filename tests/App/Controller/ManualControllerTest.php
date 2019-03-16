<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use App\Controller\ManualController;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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

    /** @var TokenStorageInterface|MockObject */
    private $tokenStorage;

    public function setUp()
    {
        $this->twig = $this->createMock(\Twig_Environment::class);
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->manualController = new ManualController($this->twig, $this->tokenStorage);
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
