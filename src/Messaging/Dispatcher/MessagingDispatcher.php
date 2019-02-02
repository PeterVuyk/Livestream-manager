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

    /** @var string */
    private $topicArn;

    /**
     * MessagingDispatcher constructor.
     * @param SnsClient $snsClient
     * @param SerializeInterface $serialize
     * @param string $topicArn
     */
    public function __construct(SnsClient $snsClient, SerializeInterface $serialize, string $topicArn)
    {
        $this->serialize = $serialize;
        $this->snsClient = $snsClient;
        $this->topicArn = $topicArn;
    }

    /**
     * @param MessageInterface $message
     * @throws PublishMessageFailedException
     */
    public function sendMessage(MessageInterface $message): void
    {
        try {
            $this->snsClient->publish([
                self::SNS_MESSAGE => $this->serialize->serialize($message),
                self::SNS_TOPIC_ARN => $this->topicArn,
            ]);
        } catch (\Exception $exception) {
            throw PublishMessageFailedException::forMessage($this->topicArn, $message->getPayload(), $exception);
        }
    }
}
