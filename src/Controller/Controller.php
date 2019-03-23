<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

abstract class Controller
{
    protected const SUCCESS_MESSAGE = 'success';
    protected const INFO_MESSAGE = 'info';
    protected const ERROR_MESSAGE = 'error';

    /** @var \Twig_Environment */
    private $twig;

    /** @var TokenStorage */
    private $tokenStorage;

    /**
     * Controller constructor.
     * @param \Twig_Environment $twig
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(\Twig_Environment $twig, TokenStorageInterface $tokenStorage)
    {
        $this->twig = $twig;
        $this->tokenStorage = $tokenStorage;
    }

    protected function getUser()
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            return;
        }

        if (!\is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return;
        }
        return $user;
    }

    protected function getUserChannel(): string
    {
        /** @var User $user */
        $user = $this->getUser();
        return $user->getChannel();
    }

    /**
     * @param string $name
     * @param array $parameters
     * @param Response|null $response
     * @return Response
     */
    protected function render($name, array $parameters = [], Response $response = null): Response
    {
        try {
            $content = $this->twig->render($name, $parameters);
        } catch (\Exception $exception) {
            $content = $exception->getMessage();
        }

        $response = $response ?? new Response();
        $response->setContent($content);

        return $response;
    }
}
