<?php
declare(strict_types=1);

namespace App\Messaging\Consumer;

use App\Exception\InvalidMessageTypeException;
use App\Exception\MessagingQueueConsumerException;
use App\Messaging\Library\MessageInterface;
use App\Service\MessageProcessor\ProcessMessageDelegator;
use Psr\Log\LoggerInterface;

class MessagingQueueWorker
{
    /** @var MessagingConsumer */
    private $messagingConsumer;

    /** @var LoggerInterface */
    private $logger;

    /** @var ProcessMessageDelegator */
    private $processMessageDelegator;

    /**
     * @param MessagingConsumer $messagingConsumer
     * @param ProcessMessageDelegator $processMessageDelegator
     * @param LoggerInterface $logger
     */
    public function __construct(
        MessagingConsumer $messagingConsumer,
        ProcessMessageDelegator $processMessageDelegator,
        LoggerInterface $logger
    ) {
        $this->messagingConsumer = $messagingConsumer;
        $this->processMessageDelegator = $processMessageDelegator;
        $this->logger = $logger;
    }

    public function __invoke()
    {
        while (true) {
            try {
                $result = $this->messagingConsumer->consume();
                $message = $this->messagingConsumer->deserializeResult($result);
            } catch (MessagingQueueConsumerException $exception) {
                $this->logger->error('Could not consume message', ['exception' => $exception]);
                continue;
            }

            if (empty($message) || !$message instanceof MessageInterface) {
                continue;
            }

            try {
                $this->processMessageDelegator->process($message);
            } catch (InvalidMessageTypeException $exception) {
                $this->logger->warning('Could not process message from worker', ['exception' => $exception]);
                continue;
            }

            try {
                $this->messagingConsumer->delete($result);
            } catch (MessagingQueueConsumerException $exception) {
                $this->logger->error('Could not delete from queue', ['exception' => $exception]);
            }
        }
    }
}
