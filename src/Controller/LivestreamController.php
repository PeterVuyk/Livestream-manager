<?php
declare(strict_types=1);

namespace App\Controller;

use App\Exception\Livestream\CouldNotApiCallBroadcastException;
use App\Exception\Messaging\PublishMessageFailedException;
use App\Messaging\Dispatcher\MessagingDispatcher;
use App\Messaging\Library\Command\StartLivestreamCommand;
use App\Messaging\Library\Command\StopLivestreamCommand;
use App\Service\Api\BroadcastApiService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class LivestreamController extends Controller
{
    /** @var MessagingDispatcher */
    private $messagingDispatcher;

    /** @var LoggerInterface */
    private $logger;

    /** @var FlashBagInterface */
    private $flashBag;

    /** @var BroadcastApiService */
    private $broadcastApiService;

    /**
     * @param MessagingDispatcher $messagingDispatcher
     * @param \Twig_Environment $twig
     * @param TokenStorageInterface $tokenStorage
     * @param LoggerInterface $logger
     * @param FlashBagInterface $flashBag
     * @param BroadcastApiService $broadcastApiService
     */
    public function __construct(
        MessagingDispatcher $messagingDispatcher,
        \Twig_Environment $twig,
        TokenStorageInterface $tokenStorage,
        LoggerInterface $logger,
        FlashBagInterface $flashBag,
        BroadcastApiService $broadcastApiService
    ) {
        parent::__construct($twig, $tokenStorage);
        $this->messagingDispatcher = $messagingDispatcher;
        $this->logger = $logger;
        $this->flashBag = $flashBag;
        $this->broadcastApiService = $broadcastApiService;
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function startStream(Request $request)
    {
        try {
            $this->messagingDispatcher->sendMessage(StartLivestreamCommand::create($this->getUserChannel()));
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
            $this->messagingDispatcher->sendMessage(StopLivestreamCommand::create($this->getUserChannel()));
            $this->flashBag->add('info', 'flash.livestream.success.stop_stream');
        } catch (PublishMessageFailedException $exception) {
            $this->logger->error('Could not start livestream', ['exception' => $exception]);
            $this->flashBag->add('error', 'flash.livestream.error.stop_stream');
        }
        return new RedirectResponse($request->headers->get('referer'));
    }

    /**
     * @return Response
     */
    public function statusStream()
    {
        try {
            $cameraStatus = $this->broadcastApiService->getStatusLivestream($this->getUserChannel());
        } catch (CouldNotApiCallBroadcastException $exception) {
            $this->logger->error('Could not get stream status', ['exception' => $exception]);
        }
        return $this->render('components/livestream.html.twig', ['cameraStatus' => $cameraStatus ?? '']);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function resetFromFailure(Request $request)
    {
        try {
            $this->broadcastApiService->resetFromFailure($this->getUserChannel());
        } catch (CouldNotApiCallBroadcastException $exception) {
            $this->logger->error('Could not reset failure status', ['exception' => $exception]);
            $this->flashBag->add('error', 'flash.livestream.error.unable_to_reset');
        }
        return new RedirectResponse($request->headers->get('referer'));
    }
}
