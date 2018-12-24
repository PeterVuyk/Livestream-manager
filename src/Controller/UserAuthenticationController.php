<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

class UserAuthenticationController implements LogoutSuccessHandlerInterface
{
    /**
     * @param Request $request
     * @return Response
     */
    public function onLogoutSuccess(Request $request): Response
    {
        return new Response('', Response::HTTP_UNAUTHORIZED);
    }
}
