<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\UserService;
use Symfony\Component\HttpFoundation\Response;

class UserManagementController extends Controller
{
    /** @var UserService */
    private $userService;

    /**
     * UserManagementController constructor.
     * @param \Twig_Environment $twig
     * @param UserService $userService
     */
    public function __construct(\Twig_Environment $twig, UserService $userService)
    {
        parent::__construct($twig);
        $this->userService = $userService;
    }

    /**
     * @return Response
     */
    public function usersList()
    {
        $users = $this->userService->getUsers();

        return $this->render('user/list.html.twig', ['users' => $users]);
    }
}
