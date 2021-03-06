<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Exception\Repository\CouldNotModifyUserException;
use App\Form\UserRegistrationType;
use App\Service\UserService;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RegistrationController extends Controller
{
    /** @var FormFactoryInterface */
    private $formFactory;
    /** @var UserService */
    private $userService;
    /** @var RouterInterface */
    private $router;

    /**
     * RegistrationController constructor.
     * @param \Twig_Environment $twig
     * @param TokenStorageInterface $tokenStorage
     * @param FormFactoryInterface $formFactory
     * @param UserService $userService
     * @param RouterInterface $router
     */
    public function __construct(
        \Twig_Environment $twig,
        TokenStorageInterface $tokenStorage,
        FormFactoryInterface $formFactory,
        UserService $userService,
        RouterInterface $router
    ) {
        parent::__construct($twig, $tokenStorage);
        $this->formFactory = $formFactory;
        $this->userService = $userService;
        $this->router = $router;
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function register(Request $request)
    {
        $form = $this->formFactory->create(
            UserRegistrationType::class,
            $user = new User(),
            ['user' => $this->getUser()]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Session $session */
            $session = $request->getSession();
            try {
                $this->userService->createUser($user);
            } catch (CouldNotModifyUserException $exception) {
                $session->getFlashBag()->add(self::ERROR_MESSAGE, 'flash.registration.error.could_not_save');
                return new RedirectResponse($request->getUri());
            }
            $session->getFlashBag()->add(self::SUCCESS_MESSAGE, 'flash.registration.success.user_created');
            return new RedirectResponse($this->router->generate('user_list'));
        }
        return $this->render(
            'user/registration/register.html.twig',
            array('form' => $form->createView())
        );
    }
}
