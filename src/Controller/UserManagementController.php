<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Exception\Repository\CouldNotModifyUserException;
use App\Exception\User\UserNotFoundException;
use App\Form\UserDetailsType;
use App\Service\UserService;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UserManagementController extends Controller
{
    /** @var UserService */
    private $userService;

    /** @var FormFactoryInterface */
    private $formFactory;

    /** @var RouterInterface */
    private $router;

    /** @var FlashBagInterface */
    private $flashBag;

    /**
     * UserManagementController constructor.
     * @param \Twig_Environment $twig
     * @param TokenStorageInterface $tokenStorage
     * @param UserService $userService
     * @param FormFactoryInterface $formFactory
     * @param RouterInterface $router
     * @param FlashBagInterface $flashBag
     */
    public function __construct(
        \Twig_Environment $twig,
        TokenStorageInterface $tokenStorage,
        UserService $userService,
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        FlashBagInterface $flashBag
    ) {
        parent::__construct($twig, $tokenStorage);
        $this->userService = $userService;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->flashBag = $flashBag;
    }

    /**
     * @return Response
     */
    public function usersList()
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user->isSuperAdmin()) {
            $users = $this->userService->getAllUsers();
        } else {
            $users = $this->userService->getUsersByChannel($user->getChannel());
        }
        return $this->render('user/list.html.twig', ['users' => $users]);
    }

    /**
     * @param int $userId
     * @return RedirectResponse
     */
    public function deleteUser(int $userId)
    {
        try {
            $this->userService->removeUser($userId);
            $this->flashBag->add(self::SUCCESS_MESSAGE, 'flash.user_management.error.user_removed');
        } catch (UserNotFoundException | CouldNotModifyUserException $exception) {
            $this->flashBag->add(self::ERROR_MESSAGE, 'flash.user_management.error.could_not_remove_user');
        }
        return new RedirectResponse($this->router->generate('user_list'));
    }

    /**
     * @param int $userId
     * @return RedirectResponse
     */
    public function toggleDisablingUser(int $userId)
    {
        try {
            $this->userService->toggleDisablingUser($userId);
        } catch (CouldNotModifyUserException | UserNotFoundException $exception) {
            $this->flashBag->add(self::ERROR_MESSAGE, 'flash.user_management.error.failed_disabling_user');
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
        $user = $this->userService->getUserById($userId);
        if (!$user instanceof User) {
            return new RedirectResponse($this->router->generate('user_list'));
        }
        $form = $this->formFactory->create(UserDetailsType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->userService->updateUser($form->getData());
                $this->flashBag->add(self::SUCCESS_MESSAGE, 'flash.user_management.success.user_created');
            } catch (CouldNotModifyUserException $exception) {
                $this->flashBag->add(self::ERROR_MESSAGE, 'flash.user_management.error.failed_saving_user');
            }
            return new RedirectResponse($request->getUri());
        }
        return $this->render('user/details.html.twig', ['user' => $user, 'form' => $form->createView()]);
    }
}
