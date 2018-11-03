<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class UserAuthenticationController
{
    /** @var RouterInterface */
    private $router;

    /**
     * UserAuthenticationController constructor.
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @return RedirectResponse
     */
    public function logout()
    {
        return new RedirectResponse($this->router->generate('home'));
    }
}
