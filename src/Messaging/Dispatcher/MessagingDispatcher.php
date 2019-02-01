<?php
declare(strict_types=1);

namespace App\Messaging\Dispatcher;

use App\Exception\PublishMessageFailedException;
use App\Messaging\Library\MessageInterface;
use App\Messaging\Serialize\SerializeInterface;
use Aws\Sns\SnsClient;

class MessagingDispatcher
{
    const SNS_MESSAGE = 'Message';
    const SNS_TOPIC_ARN = 'TargetArn';

    /** @var SnsClient */
    private $snsClient;

    /** @var SerializeInterface */
    private $serialize;

    /**
     * MessagingDispatcher constructor.
     * @param SnsClient $snsClient
     * @param SerializeInterface $serialize
     */
    public function __construct(SnsClient $snsClient, SerializeInterface $serialize)
    {
        $this->serialize = $serialize;
        $this->snsClient = $snsClient;
    }

    /**
     * @param string $topicArn
     * @param MessageInterface $message
     * @throws PublishMessageFailedException
     */
    public function sendMessage(string $topicArn, MessageInterface $message): void
    {
        try {
            $this->snsClient->publish([
                self::SNS_MESSAGE => $this->serialize->serialize($message),
                self::SNS_TOPIC_ARN => $topicArn
            ]);
        } catch (\Exception $exception) {
            throw PublishMessageFailedException::forMessage($topicArn, $message->getPayload(), $exception);
        }
    }
}
