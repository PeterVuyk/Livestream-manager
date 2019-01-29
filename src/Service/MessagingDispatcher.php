<?php
declare(strict_types=1);

namespace App\Service;

use App\Exception\PublishMessageFailedException;
use Aws\Sns\SnsClient;

class MessagingDispatcher
{
    const SNS_MESSAGE = 'Message';
    const SNS_TOPIC_ARN = 'TargetArn';

    /** @var SnsClient */
    private $snsClient;

    /**
     * MessagingDispatcher constructor.
     * @param SnsClient $snsClient
     */
    public function __construct(SnsClient $snsClient)
    {
        $this->snsClient = $snsClient;
    }

    /**
     * @param string $topicArn
     * @param string $message
     * @throws PublishMessageFailedException
     */
    public function sendMessage(string $topicArn, string $message): void
    {
        try {
            $this->snsClient->publish([self::SNS_MESSAGE => $message, self::SNS_TOPIC_ARN => $topicArn]);
        } catch (\Exception $exception) {
            throw PublishMessageFailedException::forError($topicArn, $message, $exception);
        }
    }
}
