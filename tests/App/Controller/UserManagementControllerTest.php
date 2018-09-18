<?php
declare(strict_types=1);

namespace App\Tests\App\Controller;

use App\Controller\UserManagementController;
use App\Entity\User;
use App\Service\UserService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class UserManagementControllerTest extends TestCase
{
    /** @var MockObject|\Twig_Environment */
    private $userServiceMock;

    /** @var MockObject|\Twig_Environment */
    private $twigMock;

    /** @var UserManagementController */
    private $userManagementController;

    protected function setUp()
    {
        $this->userServiceMock = $this->createMock(UserService::class);
        $this->twigMock = $this->createMock(\Twig_Environment::class);
        $this->userManagementController = new UserManagementController($this->twigMock, $this->userServiceMock);
    }

    public function testUsersList()
    {
        $this->userServiceMock->expects($this->once())->method('getUsers')->willReturn([new User()]);
        $this->twigMock->expects($this->once())->method('render')->willReturn('<p>response</p>');

        $result = $this->userManagementController->usersList();
        $this->assertSame(Response::HTTP_OK, $result->getStatusCode());
    }
}
