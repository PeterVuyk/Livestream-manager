<?php
declare(strict_types=1);

namespace App\Controller;

use App\Exception\Repository\CouldNotModifyCameraException;
use App\Exception\Messaging\PublishMessageFailedException;
use App\Messaging\Dispatcher\MessagingDispatcher;
use App\Messaging\Library\Command\StartLivestreamCommand;
use App\Messaging\Library\Command\StopLivestreamCommand;
use App\Repository\CameraRepository;
use App\Service\StreamProcessing\StreamStateMachine;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class LivestreamController extends Controller
{
    /** @var MessagingDispatcher */
    private $messagingDispatcher;

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
     * @param \Twig_Environment $twig
     * @param CameraRepository $cameraRepository
     * @param LoggerInterface $logger
     * @param FlashBagInterface $flashBag
     * @param StreamStateMachine $streamStateMachine
     */
    public function __construct(
        MessagingDispatcher $messagingDispatcher,
        \Twig_Environment $twig,
        CameraRepository $cameraRepository,
        LoggerInterface $logger,
        FlashBagInterface $flashBag,
        StreamStateMachine $streamStateMachine
    ) {
        parent::__construct($twig);
        $this->messagingDispatcher = $messagingDispatcher;
        $this->cameraRepository = $cameraRepository;
        $this->logger = $logger;
        $this->flashBag = $flashBag;
        $this->streamStateMachine = $streamStateMachine;
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function startStream(Request $request)
    {
        try {
            $this->messagingDispatcher->sendMessage(StartLivestreamCommand::create());
            $this->flashBag->add(self::INFO_MESSAGE, 'flash.livestream.success.start_stream');
        } catch (PublishMessageFailedException $exception) {
            $this->flashBag->add(self::ERROR_MESSAGE, 'flash.livestream.error.start_stream');
            $this->logger->error('Could not start livestream', ['exception' => $exception]);
        }
        return new RedirectResponse($request->headers->get('referer'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function stopStream(Request $request)
    {
        try {
            $this->messagingDispatcher->sendMessage(StopLivestreamCommand::create());
            $this->flashBag->add(self::INFO_MESSAGE, 'flash.livestream.success.stop_stream');
        } catch (PublishMessageFailedException $exception) {
            $this->logger->error('Could not start livestream', ['exception' => $exception]);
            $this->flashBag->add(self::ERROR_MESSAGE, 'flash.livestream.error.stop_stream');
        }
        return new RedirectResponse($request->headers->get('referer'));
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
     * @param Request $request
     * @return RedirectResponse
     */
    public function resetFromFailure(Request $request)
    {
        try {
            $camera = $this->cameraRepository->getMainCamera();
            $this->streamStateMachine->apply($camera, 'to_inactive');
        } catch (CouldNotModifyCameraException $exception) {
            $this->logger->error('Could not reset failure status', ['exception' => $exception]);
            $this->flashBag->add(self::ERROR_MESSAGE, 'flash.livestream.error.unable_to_reset');
        }
        return new RedirectResponse($request->headers->get('referer'));
    }
}
