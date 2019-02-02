<?php
declare(strict_types=1);

namespace App\Controller;

use App\Exception\CouldNotModifyCameraException;
use App\Exception\PublishMessageFailedException;
use App\Messaging\Dispatcher\MessagingDispatcher;
use App\Messaging\Library\Command\StartLivestreamCommand;
use App\Messaging\Library\Command\StopLivestreamCommand;
use App\Repository\CameraRepository;
use App\Service\StreamProcessing\StreamStateMachine;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class LivestreamController extends Controller
{
    /** @var MessagingDispatcher */
    private $messagingDispatcher;

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
     * @param MessagingDispatcher $messagingDispatcher
     * @param RouterInterface $router
     * @param \Twig_Environment $twig
     * @param CameraRepository $cameraRepository
     * @param LoggerInterface $logger
     * @param FlashBagInterface $flashBag
     * @param StreamStateMachine $streamStateMachine
     */
    public function __construct(
        MessagingDispatcher $messagingDispatcher,
        RouterInterface $router,
        \Twig_Environment $twig,
        CameraRepository $cameraRepository,
        LoggerInterface $logger,
        FlashBagInterface $flashBag,
        StreamStateMachine $streamStateMachine
    ) {
        parent::__construct($twig);
        $this->messagingDispatcher = $messagingDispatcher;
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
            $this->messagingDispatcher->sendMessage(StartLivestreamCommand::create());
            $this->flashBag->add(self::INFO_MESSAGE, 'flash.livestream.success.start_stream');
        } catch (PublishMessageFailedException $exception) {
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
            $this->messagingDispatcher->sendMessage(StopLivestreamCommand::create());
            $this->flashBag->add(self::INFO_MESSAGE, 'flash.livestream.success.stop_stream');
        } catch (PublishMessageFailedException $exception) {
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
