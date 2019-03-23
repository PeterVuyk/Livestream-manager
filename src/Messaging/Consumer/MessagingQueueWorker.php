<?php
declare(strict_types=1);

namespace App\Messaging\Consumer;

use App\Exception\Messaging\InvalidMessageTypeException;
use App\Exception\Messaging\MessagingQueueConsumerException;
use App\Messaging\Library\MessageInterface;
use App\Messaging\Serialize\DeserializeInterface;
use App\Service\ProcessMessageDelegator;
use Psr\Log\LoggerInterface;

class MessagingQueueWorker
{
    /** @var MessagingConsumer */
    private $messagingConsumer;

    /** @var LoggerInterface */
    private $logger;

    /** @var ProcessMessageDelegator */
    private $processMessageDelegator;

    /** @var DeserializeInterface */
    private $deserializer;

    /**
     * @param MessagingConsumer $messagingConsumer
     * @param ProcessMessageDelegator $processMessageDelegator
     * @param LoggerInterface $logger
     * @param DeserializeInterface $deserializer
     */
    public function __construct(
        MessagingConsumer $messagingConsumer,
        ProcessMessageDelegator $processMessageDelegator,
        LoggerInterface $logger,
        DeserializeInterface $deserializer
    ) {
        $this->messagingConsumer = $messagingConsumer;
        $this->processMessageDelegator = $processMessageDelegator;
        $this->logger = $logger;
        $this->deserializer = $deserializer;
    }

    public function __invoke(int $numberRetriesQueue)
    {
        $retryCount = 0;
        while ($retryCount < $numberRetriesQueue) {
            $retryCount++;

            try {
                $payload = $this->messagingConsumer->consume();
                $message = $this->deserializer->deserialize($payload);
            } catch (MessagingQueueConsumerException $exception) {
                $this->logger->error('Could not consume message', ['exception' => $exception]);
                continue;
            }
            if (!empty($payload) && !$message instanceof MessageInterface) {
                try {
                    $this->messagingConsumer->delete($payload);
                } catch (MessagingQueueConsumerException $exception) {
                    $this->logger->error('Could not delete from queue', ['exception' => $exception]);
                }
            }

            if (!$message instanceof MessageInterface) {
                continue;
            }

            try {
                $this->processMessageDelegator->process($message);
            } catch (InvalidMessageTypeException $exception) {
                $this->logger->warning('Could not process message from worker', ['exception' => $exception]);
                continue;
            }

            try {
                $this->messagingConsumer->delete($payload);
            } catch (MessagingQueueConsumerException $exception) {
                $this->logger->error('Could not delete from queue', ['exception' => $exception]);
            }
            continue;
        }
        $this->logger->info("Number of retries exceeded, Terminated");
    }
}
