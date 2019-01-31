<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

abstract class Controller
{
    protected const SUCCESS_MESSAGE = 'success';
    protected const NOTICE_MESSAGE = 'notice';
    protected const ERROR_MESSAGE = 'error';

    /** @var \Twig_Environment */
    private $twig;

    /**
     * Controller constructor.
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
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
