<?php
declare(strict_types=1);

namespace App\Controller;

use App\Exception\CouldNotModifyCameraException;
use App\Exception\CouldNotStartLivestreamException;
use App\Repository\CameraRepository;
use App\Service\StreamProcessing\StartStreamService;
use App\Service\StreamProcessing\StopStreamService;
use App\Service\StreamProcessing\StreamStateMachine;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class LivestreamController extends Controller
{
    /** @var StartStreamService */
    private $startStreamService;

    /** @var StopStreamService */
    private $stopStreamService;

    /** @var RouterInterface */
    private $router;

    /** @var CameraRepository */
    private $cameraRepository;

    /** @var LoggerInterface */
    private $logger;

    /** @var FlashBagInterface */
    private $flashBag;

    /** @var StreamStateMachine */
    private $streamStateMachine;

    /**
     * LivestreamController constructor.
     * @param StartStreamService $startStreamService
     * @param StopStreamService $stopStreamService
     * @param RouterInterface $router
     * @param \Twig_Environment $twig
     * @param CameraRepository $cameraRepository
     * @param LoggerInterface $logger
     * @param FlashBagInterface $flashBag
     * @param StreamStateMachine $streamStateMachine
     */
    public function __construct(
        StartStreamService $startStreamService,
        StopStreamService $stopStreamService,
        RouterInterface $router,
        \Twig_Environment $twig,
        CameraRepository $cameraRepository,
        LoggerInterface $logger,
        FlashBagInterface $flashBag,
        StreamStateMachine $streamStateMachine
    ) {
        parent::__construct($twig);
        $this->startStreamService = $startStreamService;
        $this->stopStreamService = $stopStreamService;
        $this->router = $router;
        $this->cameraRepository = $cameraRepository;
        $this->logger = $logger;
        $this->flashBag = $flashBag;
        $this->streamStateMachine = $streamStateMachine;
    }

    /**
     * @return RedirectResponse
     */
    public function startStream()
    {
        try {
            $this->startStreamService->process();
        } catch (CouldNotStartLivestreamException $exception) {
            $this->flashBag->add(self::ERROR_MESSAGE, 'flash.livestream.error.start_stream');
            $this->logger->error('Could not start livestream', ['exception' => $exception]);
        }
        return new RedirectResponse($this->router->generate('scheduler_list'));
    }

    /**
     * @return RedirectResponse
     */
    public function stopStream()
    {
        try {
            $this->stopStreamService->process();
        } catch (CouldNotModifyCameraException $exception) {
            $this->logger->error('Could not start livestream', ['exception' => $exception]);
            $this->flashBag->add(self::ERROR_MESSAGE, 'flash.livestream.error.stop_stream');
        }
        return new RedirectResponse($this->router->generate('scheduler_list'));
    }

    /**
     * @return Response
     */
    public function statusStream()
    {
        $camera = $this->cameraRepository->getMainCamera();
        return $this->render('components/livestream.html.twig', ['camera' => $camera]);
    }

    /**
     * @return RedirectResponse
     */
    public function resetFromFailure()
    {
        try {
            $camera = $this->cameraRepository->getMainCamera();
            $this->streamStateMachine->apply($camera, 'to_inactive');
        } catch (CouldNotModifyCameraException $exception) {
            $this->logger->error('Could not reset failure status', ['exception' => $exception]);
            $this->flashBag->add(self::ERROR_MESSAGE, 'flash.livestream.error.unable_to_reset');
        }
        return new RedirectResponse($this->router->generate('scheduler_list'));
    }
}
