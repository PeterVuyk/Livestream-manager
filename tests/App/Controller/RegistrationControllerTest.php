<?php
declare(strict_types=1);

namespace App\Tests\App\Controller;

use App\Controller\RegistrationController;
use App\Service\UserService;
use Doctrine\ORM\ORMException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;

class RegistrationControllerTest extends TestCase
{
    /** @var MockObject|\Twig_Environment */
    private $twigMock;

    /** @var MockObject|FormFactoryInterface */
    private $formFactory;

    /** @var MockObject|UserService */
    private $userService;

    /** @var MockObject|RouterInterface */
    private $router;

    /** @var RegistrationController */
    private $registrationController;

    public function setUp()
    {
        $twigMock = $this->twigMock = $this->createMock(\Twig_Environment::class);
        $formFactoryMock = $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $userService = $this->userService = $this->createMock(UserService::class);
        $router = $this->router = $this->createMock(RouterInterface::class);
        $this->registrationController = new RegistrationController($twigMock, $formFactoryMock, $userService, $router);
    }

    public function testRegisterOpenPage()
    {
        $formMock = $this->createMock(FormInterface::class);
        $formMock->expects($this->once())->method('handleRequest');
        $formMock->expects($this->once())->method('isSubmitted')->willReturn(false);
        $formFactoryMock = $this->formFactory;
        $formFactoryMock->expects($this->once())->method('create')->willReturn($formMock);

        $twigMock = $this->twigMock;
        $twigMock->expects($this->once())->method('render')->willReturn('<p>page</p>');

        $response = $this->registrationController->register(new Request());
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testRegisterSubmitSuccess()
    {
        $formMock = $this->createMock(FormInterface::class);
        $formMock->expects($this->once())->method('handleRequest');
        $formMock->expects($this->once())->method('isSubmitted')->willReturn(true);
        $formMock->expects($this->once())->method('isValid')->willReturn(true);
        $this->formFactory->expects($this->once())->method('create')->willReturn($formMock);

        $this->userService->expects($this->once())->method('createUser');

        $flashBagMock = $this->createMock(FlashBagInterface::class);
        $flashBagMock->expects($this->once())->method('add');
        $sessionMock = $this->createMock(Session::class);
        $sessionMock->method('getFlashBag')->willReturn($flashBagMock);
        ($request = new Request())->setSession($sessionMock);

        $this->router->expects($this->once())->method('generate')->willReturn('url');

        $response = $this->registrationController->register($request);
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }

    public function testRegisterSubmitCouldNotSaveUser()
    {
        $formMock = $this->createMock(FormInterface::class);
        $formMock->expects($this->once())->method('handleRequest');
        $formMock->expects($this->once())->method('isSubmitted')->willReturn(true);
        $formMock->expects($this->once())->method('isValid')->willReturn(true);
        $this->formFactory->expects($this->once())->method('create')->willReturn($formMock);

        $this->userService->expects($this->once())->method('createUser')->willThrowException(new ORMException());

        $flashBagMock = $this->createMock(FlashBagInterface::class);
        $flashBagMock->expects($this->once())->method('add');
        $sessionMock = $this->createMock(Session::class);
        $sessionMock->method('getFlashBag')->willReturn($flashBagMock);
        ($request = new Request())->setSession($sessionMock);

        $response = $this->registrationController->register($request);
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }
}
