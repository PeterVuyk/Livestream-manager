<?php
declare(strict_types=1);

namespace App\Messaging\Consumer;

use App\Exception\Messaging\MessagingQueueConsumerException;
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
     * @return Result
     * @throws MessagingQueueConsumerException
     */
    public function consume(): Result
    {
        try {
            $result = $this->sqsClient->receiveMessage([
                'QueueUrl' => $this->queueUrl,
                'MaxNumberOfMessages' => 1,
                'ReceiveMessageWaitTimeSeconds' => 20
            ]);
        } catch (AwsException $awsException) {
            throw MessagingQueueConsumerException::fromError($awsException);
        }
        return $result;
    }

    /**
     * @param Result $result
     * @return MessageInterface|null
     */
    public function deserializeResult(Result $result): ?MessageInterface
    {
        if ($result->get('Messages') !== null) {
            $message = $this->deserializer->deserialize($result->get('Messages')[0]);
        }
        return $message ?? null;
    }

    /**
     * @param Result $result
     * @throws MessagingQueueConsumerException
     */
    public function delete(Result $result): void
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
