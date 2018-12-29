<?php
declare(strict_types=1);

namespace App\Tests\App\Controller;

use App\Controller\UserManagementController;
use App\Entity\User;
use App\Service\UserService;
use Doctrine\ORM\ORMException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class UserManagementControllerTest extends TestCase
{
    /** @var \Twig_Environment|MockObject */
    private $twigMock;

    /** @var UserService|MockObject */
    private $userServiceMock;

    /** @var FormFactoryInterface|MockObject */
    private $formFactoryMock;

    /** @var RouterInterface|MockObject */
    private $routerMock;

    /** @var FlashBagInterface|MockObject */
    private $flashBagMock;

    /** @var UserManagementController */
    private $userManagementController;

    public function setUp()
    {
        $this->twigMock = $this->createMock(\Twig_Environment::class);
        $this->userServiceMock = $this->createMock(UserService::class);
        $this->formFactoryMock = $this->createMock(FormFactoryInterface::class);
        $this->routerMock = $this->createMock(RouterInterface::class);
        $this->flashBagMock = $this->createMock(FlashBagInterface::class);
        $this->userManagementController = new UserManagementController(
            $this->twigMock,
            $this->userServiceMock,
            $this->formFactoryMock,
            $this->routerMock,
            $this->flashBagMock
        );
    }

    public function testUsersList()
    {
        $this->userServiceMock->expects($this->once())->method('getAllUsers')->willReturn([new User()]);
        $this->twigMock->expects($this->once())->method('render')->willReturn('<p>hi</p>');

        $response = $this->userManagementController->usersList();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testDeleteUserSuccess()
    {
        $this->userServiceMock->expects($this->once())->method('removeUser');
        $this->flashBagMock->expects($this->once())->method('add');
        $this->routerMock->expects($this->once())->method('generate')->willReturn('direction');

        $response = $this->userManagementController->deleteUser(1);
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }

    public function testDeleteUserFailed()
    {
        $this->userServiceMock->expects($this->once())->method('removeUser')->willThrowException(new \Exception());
        $this->flashBagMock->expects($this->once())->method('add');
        $this->routerMock->expects($this->once())->method('generate')->willReturn('direction');

        $response = $this->userManagementController->deleteUser(1);
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }

    public function testToggleDisablingUserSuccess()
    {
        $this->userServiceMock->expects($this->once())->method('toggleDisablingUser');
        $this->routerMock->expects($this->once())->method('generate')->willReturn('direction');
        $this->flashBagMock->expects($this->never())->method('add');

        $response = $this->userManagementController->toggleDisablingUser(3);
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }

    public function testToggleDisablingUserFailed()
    {
        $this->userServiceMock->expects($this->once())->method('toggleDisablingUser')->willThrowException(new \Exception());
        $this->routerMock->expects($this->once())->method('generate')->willReturn('direction');
        $this->flashBagMock->expects($this->once())->method('add');

        $response = $this->userManagementController->toggleDisablingUser(3);
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }

    public function testUserDetailsOpeningPage()
    {
        $this->userServiceMock->expects($this->once())->method('getUserById')->willReturn(new User());

        $formMock = $this->createMock(FormInterface::class);
        $formMock->expects($this->once())->method('handleRequest');
        $formMock->expects($this->once())->method('createView');
        $formMock->expects($this->once())->method('isSubmitted')->willReturn(false);
        $this->formFactoryMock->expects($this->once())->method('create')->willReturn($formMock);

        $this->twigMock->expects($this->once())->method('render')->willReturn('<p>hi</p>');

        $response = $this->userManagementController->userDetails(3, new Request());
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testUserDetailsFailedCollectDetails()
    {
        $this->userServiceMock->expects($this->once())->method('getUserById')->willReturn(null);
        $this->formFactoryMock->expects($this->never())->method('create');
        $this->routerMock->expects($this->once())->method('generate')->willReturn('direction');

        $response = $this->userManagementController->userDetails(3, new Request());
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }

    public function testUserDetailsSubmitFormSuccess()
    {
        $this->userServiceMock->expects($this->once())->method('getUserById')->willReturn(new User());
        $this->userServiceMock->expects($this->once())->method('updateUser');
        $this->flashBagMock->expects($this->once())->method('add');

        $formMock = $this->createMock(FormInterface::class);
        $formMock->expects($this->once())->method('handleRequest');
        $formMock->expects($this->once())->method('getData')->willReturn(new User());
        $formMock->expects($this->once())->method('isSubmitted')->willReturn(true);
        $formMock->expects($this->once())->method('isValid')->willReturn(true);
        $this->formFactoryMock->expects($this->once())->method('create')->willReturn($formMock);

        $response = $this->userManagementController->userDetails(3, new Request());
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }

    public function testUserDetailsSubmitFormFailed()
    {
        $this->userServiceMock->expects($this->once())->method('getUserById')->willReturn(new User());
        $this->userServiceMock->expects($this->once())->method('updateUser')->willThrowException(new ORMException());
        $this->flashBagMock->expects($this->once())->method('add');

        $formMock = $this->createMock(FormInterface::class);
        $formMock->expects($this->once())->method('handleRequest');
        $formMock->expects($this->once())->method('getData')->willReturn(new User());
        $formMock->expects($this->once())->method('isSubmitted')->willReturn(true);
        $formMock->expects($this->once())->method('isValid')->willReturn(true);
        $this->formFactoryMock->expects($this->once())->method('create')->willReturn($formMock);

        $response = $this->userManagementController->userDetails(3, new Request());
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }
}
