<?php
declare(strict_types=1);

namespace App\EventSubscriber;

use App\Exception\Messaging\PublishMessageFailedException;
use App\Messaging\Dispatcher\MessagingDispatcher;
use App\Messaging\Library\Event\CameraStateChangedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class CameraStateChangedSubscriber implements EventSubscriberInterface
{
    /** @var LoggerInterface */
    private $logger;

    /** @var MessagingDispatcher */
    private $messagingDispatcher;

    /**
     * FailureStateSubscriber constructor.
     * @param LoggerInterface $logger
     * @param MessagingDispatcher $messagingDispatcher
     */
    public function __construct(
        LoggerInterface $logger,
        MessagingDispatcher $messagingDispatcher
    ) {
        $this->logger = $logger;
        $this->messagingDispatcher = $messagingDispatcher;
    }

    /**
     * @param Event $event
     */
    public function sendCameraStateChangedEvent(Event $event): void
    {
        $previousState = $event->getTransition()->getFroms();
        $newState = $event->getTransition()->getTos();

        $cameraStateChangedEvent = CameraStateChangedEvent::create(current($previousState), current($newState));

        try {
            $this->messagingDispatcher->sendMessage($cameraStateChangedEvent);
        } catch (PublishMessageFailedException $exception) {
            $this->logger->error($exception->getMessage(), ['exception' => $exception]);
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return ['workflow.camera_stream.entered'=> 'sendCameraStateChangedEvent'];
    }
}
