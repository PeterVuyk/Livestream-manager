<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\StreamProcessing\StartStreamService;
use App\Service\StreamProcessing\StopStreamService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class LivestreamController
{
    /** @var StartStreamService */
    private $startStreamService;

    /** @var StopStreamService */
    private $stopStreamService;

    /** @var RouterInterface */
    private $router;

    /**
     * LivestreamController constructor.
     * @param StartStreamService $startStreamService
     * @param StopStreamService $stopStreamService
     * @param RouterInterface $router
     */
    public function __construct(
        StartStreamService $startStreamService,
        StopStreamService $stopStreamService,
        RouterInterface $router
    ) {
        $this->startStreamService = $startStreamService;
        $this->stopStreamService = $stopStreamService;
        $this->router = $router;
    }

    /**
     * @return RedirectResponse
     */
    public function startStream()
    {
        $this->startStreamService->process();
        return new RedirectResponse($this->router->generate('scheduler_list'));
    }

    /**
     * @return RedirectResponse
     */
    public function stopStream()
    {
        $this->stopStreamService->process();
        return new RedirectResponse($this->router->generate('scheduler_list'));
    }
}
