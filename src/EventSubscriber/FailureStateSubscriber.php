<?php
declare(strict_types=1);

namespace App\EventSubscriber;

use App\Exception\PublishMessageFailedException;
use App\Messaging\Dispatcher\MessagingDispatcher;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class FailureStateSubscriber implements EventSubscriberInterface
{
    /** @var LoggerInterface */
    private $logger;

    /** @var MessagingDispatcher */
    private $messagingDispatcher;

    /** @var string */
    private $topicArn;

    /**
     * FailureStateSubscriber constructor.
     * @param LoggerInterface $logger
     * @param MessagingDispatcher $messagingDispatcher
     * @param string $topicArn
     */
    public function __construct(
        LoggerInterface $logger,
        MessagingDispatcher $messagingDispatcher,
        string $topicArn
    ) {
        $this->logger = $logger;
        $this->messagingDispatcher = $messagingDispatcher;
        $this->topicArn = $topicArn;
    }

    /**
     * @param Event $event
     */
    public function alertForFailure(Event $event): void
    {
        $message = sprintf(
            'Camera (id: "%s") performed transaction "%s" from "%s" to "%s"',
            $event->getSubject()->getCamera(),
            $event->getTransition()->getName(),
            implode(', ', array_keys($event->getMarking()->getPlaces())),
            implode(', ', $event->getTransition()->getTos())
        );

        $this->logger->alert($message);

        try {
            $this->messagingDispatcher->sendMessage($this->topicArn, $message);
        } catch (PublishMessageFailedException $exception) {
            $this->logger->error($exception->getMessage());
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return ['workflow.camera_stream.enter.failure'=> 'alertForFailure'];
    }
}
