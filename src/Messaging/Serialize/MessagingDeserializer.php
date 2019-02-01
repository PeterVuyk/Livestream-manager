<?php
declare(strict_types=1);

namespace App\Messaging\Serialize;

use App\Exception\UnsupportedMessageException;
use App\Messaging\Library\Command\StartLivestreamCommand;
use App\Messaging\Library\Command\StopLivestreamCommand;
use App\Messaging\Library\MessageInterface;
use Webmozart\Assert\Assert;

class MessagingDeserializer implements DeserializeInterface
{
    /**
     * @param array $payload
     * @return MessageInterface
     * @throws UnsupportedMessageException
     */
    public function deserialize(array $payload): MessageInterface
    {
        Assert::keyExists($payload, MessageInterface::RESOURCE_ID);
        Assert::keyExists($payload, MessageInterface::RESOURCE_ID_KEY);
        /** @var MessageInterface $className */
        $className = $this->getClassNameFromMessage($payload);

        return $className::createFromPayload($payload);
    }

    /**
     * @param array $payload
     * @return string
     * @throws UnsupportedMessageException
     */
    private function getClassNameFromMessage(array $payload): string
    {
        switch ($payload[MessageInterface::RESOURCE_ID_KEY]) {
            case StartLivestreamCommand::RESOURCE:
                $className = StartLivestreamCommand::class;
                break;
            case StopLivestreamCommand::RESOURCE:
                $className = StopLivestreamCommand::class;
                break;
            default:
                throw UnsupportedMessageException::classNotFound($payload);
        }
        return $className;
    }
}
