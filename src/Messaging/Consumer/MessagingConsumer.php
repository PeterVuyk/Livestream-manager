<?php
declare(strict_types=1);

namespace App\Messaging\Consumer;

use App\Exception\Messaging\MessagingQueueConsumerException;
use Aws\Exception\AwsException;
use Aws\Sqs\SqsClient;

class MessagingConsumer
{
    /** @var SqsClient */
    private $sqsClient;

    /** @var string */
    private $queueUrl;

    /**
     * MessagingConsumer constructor.
     * @param SqsClient $sqsClient
     * @param string $queueUrl
     */
    public function __construct(SqsClient $sqsClient, string $queueUrl)
    {
        $this->sqsClient = $sqsClient;
        $this->queueUrl = $queueUrl;
    }

    /**
     * @return array
     * @throws MessagingQueueConsumerException
     */
    public function consume(): array
    {
        try {
            $result = $this->sqsClient->receiveMessage([
                'QueueUrl' => $this->queueUrl,
                'MaxNumberOfMessages' => 1,
                'ReceiveMessageWaitTimeSeconds' => 20,
            ]);
        } catch (AwsException $awsException) {
            throw MessagingQueueConsumerException::fromError($awsException);
        }
        $payload = [];
        if ($result->get('Messages') !== null) {
            $payload = $result->get('Messages')[0];
        }
        return $payload;
    }

    /**
     * @param array $payload
     * @throws MessagingQueueConsumerException
     */
    public function delete(array $payload): void
    {
        try {
            $this->sqsClient->deleteMessage([
                'QueueUrl'      => $this->queueUrl,
                'ReceiptHandle' => $payload['ReceiptHandle']
            ]);
        } catch (AwsException $awsException) {
            throw MessagingQueueConsumerException::fromError($awsException);
        }
    }
}
