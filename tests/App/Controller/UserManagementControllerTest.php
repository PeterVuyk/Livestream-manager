<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use App\Controller\UserManagementController;
use App\Entity\User;
use App\Exception\UserNotFoundException;
use App\Service\UserService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;

class UserManagementControllerTest extends TestCase
{
    /** @var MockObject|\Twig_Environment */
    private $userServiceMock;

    /** @var MockObject|\Twig_Environment */
    private $twigMock;

    /** @var MockObject|UserManagementController */
    private $userManagementController;

    /** @var MockObject|FormFactoryInterface */
    private $formFactoryMock;

    /** @var MockObject|RouterInterface */
    private $routerMock;

    protected function setUp()
    {
        $this->formFactoryMock = $this->createMock(FormFactoryInterface::class);
        $this->routerMock = $this->createMock(RouterInterface::class);
        $this->userServiceMock = $this->createMock(UserService::class);
        $this->twigMock = $this->createMock(\Twig_Environment::class);
        $this->userManagementController = new UserManagementController(
            $this->twigMock,
            $this->userServiceMock,
            $this->formFactoryMock,
            $this->routerMock
        );
    }

    public function testToggleDisablingUserFailed()
    {
        $flashBagInterfaceMock = $this->createMock(FlashBagInterface::class);
        $flashBagInterfaceMock->expects($this->once())->method('add');
        $sessionMock = $this->createMock(Session::class);
        $sessionMock->expects($this->once())->method('getFlashBag')->willReturn($flashBagInterfaceMock);
        $request = new Request();
        $request->setSession($sessionMock);

        $exception = UserNotFoundException::couldNotToggleDisablingUser(3);
        $this->userServiceMock->expects($this->once())->method('toggleDisablingUser')->willThrowException($exception);
        $this->routerMock->expects($this->once())->method('generate')->willReturn('url');

        $result = $this->userManagementController->toggleDisablingUser(3, $request);
        $this->assertSame(Response::HTTP_FOUND, $result->getStatusCode());
    }

    public function testToggleDisablingUserSuccess()
    {
        $flashBagInterfaceMock = $this->createMock(FlashBagInterface::class);
        $sessionMock = $this->createMock(Session::class);
        $sessionMock->expects($this->never())->method('getFlashBag')->willReturn($flashBagInterfaceMock);
        $request = new Request();
        $request->setSession($sessionMock);

        $this->userServiceMock->expects($this->once())->method('toggleDisablingUser');
        $this->routerMock->expects($this->once())->method('generate')->willReturn('url');

        $result = $this->userManagementController->toggleDisablingUser(3, $request);
        $this->assertSame(Response::HTTP_FOUND, $result->getStatusCode());
    }

    public function testDeleteUserFailed()
    {
        $flashBagInterfaceMock = $this->createMock(FlashBagInterface::class);
        $flashBagInterfaceMock->expects($this->once())->method('add');
        $sessionMock = $this->createMock(Session::class);
        $sessionMock->expects($this->once())->method('getFlashBag')->willReturn($flashBagInterfaceMock);
        $request = new Request();
        $request->setSession($sessionMock);

        $exception = UserNotFoundException::couldNotRemoveUser(3);
        $this->userServiceMock->expects($this->once())->method('removeUser')->willThrowException($exception);
        $this->routerMock->expects($this->once())->method('generate')->willReturn('url');

        $result = $this->userManagementController->deleteUser(3, $request);
        $this->assertSame(Response::HTTP_FOUND, $result->getStatusCode());
    }

    public function testDeleteUserSuccess()
    {
        $flashBagInterfaceMock = $this->createMock(FlashBagInterface::class);
        $sessionMock = $this->createMock(Session::class);
        $sessionMock->expects($this->never())->method('getFlashBag')->willReturn($flashBagInterfaceMock);
        $request = new Request();
        $request->setSession($sessionMock);

        $this->userServiceMock->expects($this->once())->method('removeUser');
        $this->routerMock->expects($this->once())->method('generate')->willReturn('url');

        $result = $this->userManagementController->deleteUser(3, $request);
        $this->assertSame(Response::HTTP_FOUND, $result->getStatusCode());
    }

    public function testUsersList()
    {
        $this->userServiceMock->expects($this->once())->method('getAllUsers')->willReturn([new User()]);
        $this->twigMock->expects($this->once())->method('render')->willReturn('<p>response</p>');

        $result = $this->userManagementController->usersList();
        $this->assertSame(Response::HTTP_OK, $result->getStatusCode());
    }

    public function testUserDetailsLoadPage()
    {
        $this->userServiceMock->expects($this->once())->method('getUserById')->willReturn(new User());

        $formInterfaceMock = $this->createMock(FormInterface::class);
        $this->formFactoryMock->expects($this->once())->method('create')->willReturn($formInterfaceMock);
        $formInterfaceMock->expects($this->once())->method('isSubmitted')->willReturn(false);

        $this->twigMock->expects($this->once())->method('render')->willReturn('<p>response</p>');

        $response = $this->userManagementController->userDetails(3, new Request());
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testUserDetailsFormSubmitted()
    {
        $flashBagInterfaceMock = $this->createMock(FlashBagInterface::class);
        $flashBagInterfaceMock->expects($this->once())->method('add');
        $sessionMock = $this->createMock(Session::class);
        $sessionMock->expects($this->once())->method('getFlashBag')->willReturn($flashBagInterfaceMock);
        $request = new Request();
        $request->setSession($sessionMock);

        $this->userServiceMock->expects($this->once())->method('getUserById')->willReturn(new User());
        $this->userServiceMock->expects($this->once())->method('updateUser');

        $formInterfaceMock = $this->createMock(FormInterface::class);
        $formInterfaceMock->expects($this->once())->method('handleRequest');
        $formInterfaceMock->expects($this->once())->method('isSubmitted')->willReturn(true);
        $formInterfaceMock->expects($this->once())->method('isValid')->willReturn(true);
        $formInterfaceMock->expects($this->once())->method('getData')->willReturn(new User());
        $this->formFactoryMock->expects($this->once())->method('create')->willReturn($formInterfaceMock);

        $response = $this->userManagementController->userDetails(3, $request);
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }

    public function testUserDetailsNoUser()
    {
        $this->userServiceMock->expects($this->once())->method('getUserById')->willReturn(null);
        $this->routerMock->expects($this->once())->method('generate')->willReturn('url');
        $response = $this->userManagementController->userDetails(3, new Request());
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }
}
