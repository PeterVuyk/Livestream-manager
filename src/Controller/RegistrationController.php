<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\UserRegistrationType;
use App\Service\UserService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;

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
     * @param FormFactoryInterface $formFactory
     * @param UserService $userService
     * @param RouterInterface $router
     */
    public function __construct(
        \Twig_Environment $twig,
        FormFactoryInterface $formFactory,
        UserService $userService,
        RouterInterface $router
    ) {
        parent::__construct($twig);
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
        $form = $this->formFactory->create(UserRegistrationType::class, $user = new User());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Session $session */
            $session = $request->getSession();
            try {
                $this->userService->createUser($user);
            } catch (ORMException | OptimisticLockException $exception) {
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
