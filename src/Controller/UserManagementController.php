<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\UserDetailsType;
use App\Service\UserService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;

class UserManagementController extends Controller
{
    /** @var UserService */
    private $userService;

    /** @var FormFactoryInterface */
    private $formFactory;

    /** @var RouterInterface */
    private $router;

    /**
     * UserManagementController constructor.
     * @param \Twig_Environment $twig
     * @param UserService $userService
     * @param FormFactoryInterface $formFactory
     * @param RouterInterface $router
     */
    public function __construct(
        \Twig_Environment $twig,
        UserService $userService,
        FormFactoryInterface $formFactory,
        RouterInterface $router
    ) {
        parent::__construct($twig);
        $this->userService = $userService;
        $this->formFactory = $formFactory;
        $this->router = $router;
    }

    /**
     * @return Response
     */
    public function usersList()
    {
        $users = $this->userService->getAllUsers();

        return $this->render('user/list.html.twig', ['users' => $users]);
    }

    /**
     * @param int $userId
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteUser(int $userId, Request $request)
    {
        try {
            $this->userService->removeUser($userId);
        } catch (\Exception $exception) {
            /** @var Session $session */
            $session = $request->getSession();
            $session->getFlashBag()->add(self::ERROR_MESSAGE, 'Could not remove user.');
        }
        return new RedirectResponse($this->router->generate('user_list'));
    }

    /**
     * @param int $userId
     * @param Request $request
     * @return RedirectResponse
     */
    public function toggleDisablingUser(int $userId, Request $request)
    {
        try {
            $this->userService->toggleDisablingUser($userId);
        } catch (\Exception $exception) {
            /** @var Session $session */
            $session = $request->getSession();
            $session->getFlashBag()->add(self::ERROR_MESSAGE, 'Unable to toggle the disable status from the user.');
        }

        return new RedirectResponse($this->router->generate('user_list'));
    }

    /**
     * @param int $userId
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function userDetails(int $userId, Request $request)
    {
        /** @var Session $session */
        $session = $request->getSession();
        $user = $this->userService->getUserById($userId);
        if (!$user instanceof User) {
            return new RedirectResponse($this->router->generate('user_list'));
        }
        $form = $this->formFactory->create(UserDetailsType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->userService->updateUser($form->getData());
                $session->getFlashBag()->add(self::SUCCESS_MESSAGE, 'User successfully updated.');
            } catch (ORMException | OptimisticLockException $exception) {
                $session->getFlashBag()->add(self::ERROR_MESSAGE, 'Could not update the user.');
            }
            return new RedirectResponse($request->getUri());
        }
        return $this->render('user/details.html.twig', ['user' => $user, 'form' => $form->createView()]);
    }
}
