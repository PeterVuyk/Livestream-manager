<?php
declare(strict_types=1);

namespace App\Tests\App\Authentication;

use App\Controller\UserAuthenticationController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserAuthenticationControllerTest extends TestCase
{
    /** @var UserAuthenticationController */
    private $userAuthenticationController;

    public function setUp()
    {
        $this->userAuthenticationController = new UserAuthenticationController();
    }

    public function testOnLogoutSuccess()
    {
        $result = $this->userAuthenticationController->onLogoutSuccess(new Request());
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $result->getStatusCode());
    }
}
