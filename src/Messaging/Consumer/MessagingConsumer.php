<?php
declare(strict_types=1);

namespace App\Messaging\Consumer;

use App\Exception\MessagingQueueConsumerException;
use App\Messaging\Library\MessageInterface;
use App\Messaging\Serialize\DeserializeInterface;
use Aws\Exception\AwsException;
use Aws\Result;
use Aws\Sqs\SqsClient;

class MessagingConsumer
{
    /** @var SqsClient */
    private $sqsClient;

    /** @var DeserializeInterface */
    private $deserializer;

    /** @var string */
    private $queueUrl;

    /**
     * MessagingConsumer constructor.
     * @param SqsClient $sqsClient
     * @param DeserializeInterface $deserializer
     * @param string $queueUrl
     */
    public function __construct(SqsClient $sqsClient, DeserializeInterface $deserializer, string $queueUrl)
    {
        $this->sqsClient = $sqsClient;
        $this->deserializer = $deserializer;
        $this->queueUrl = $queueUrl;
    }

    /**
     * @return null|MessageInterface
     * @throws MessagingQueueConsumerException
     */
    public function consume(): ?MessageInterface
    {
        try {
            $result = $this->sqsClient->receiveMessage([
                'QueueUrl' => $this->queueUrl,
                'MaxNumberOfMessages' => 1,
                'ReceiveMessageWaitTimeSeconds' => 20
            ]);
            if ($result->get('Messages') !== null) {

                $message = $this->deserializer->deserialize($result->get('Messages')[0]);
                $this->delete($result);
            }
        } catch (AwsException $awsException) {
            throw MessagingQueueConsumerException::fromError($awsException);
        }
        return $message ?? null;
    }

    /**
     * @param Result $result
     * @throws MessagingQueueConsumerException
     */
    private function delete(Result $result): void
    {
        try {
            $this->sqsClient->deleteMessage([
                'QueueUrl'      => $this->queueUrl,
                'ReceiptHandle' => $result->get('Messages')[0]['ReceiptHandle']
            ]);
        } catch (AwsException $awsException) {
            throw MessagingQueueConsumerException::fromError($awsException);
        }
    }
}
