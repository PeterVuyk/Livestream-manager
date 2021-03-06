<?php
declare(strict_types=1);

namespace App\Messaging\Serialize;

use App\Exception\Messaging\UnsupportedMessageException;
use App\Messaging\Library\Command\StartLivestreamCommand;
use App\Messaging\Library\Command\StopLivestreamCommand;
use App\Messaging\Library\Event\CameraStateChangedEvent;
use App\Messaging\Library\MessageInterface;
use Webmozart\Assert\Assert;

class MessagingDeserializer implements DeserializeInterface
{
    const MESSAGE_BODY = 'Body';
    const MESSAGE_PAYLOAD = 'Message';

    /**
     * @param array $message
     * @return MessageInterface|null
     * @throws UnsupportedMessageException
     */
    public function deserialize(array $message): ?MessageInterface
    {
        try {
            $payload = $this->validate($message);
        } catch (\InvalidArgumentException $exception) {
            return null;
        }
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
            case CameraStateChangedEvent::RESOURCE:
                $className = CameraStateChangedEvent::class;
                break;
            default:
                throw UnsupportedMessageException::classNotFound($payload);
        }
        return $className;
    }

    /**
     * @param array $message
     * @return array
     */
    private function validate(array $message): array
    {
        Assert::keyExists($message, self::MESSAGE_BODY);

        $body = json_decode($message[self::MESSAGE_BODY], true);
        Assert::isArray($body);
        Assert::keyExists($body, self::MESSAGE_PAYLOAD);

        $payload = json_decode($body[self::MESSAGE_PAYLOAD], true);
        Assert::isArray($payload);
        Assert::keyExists($payload, MessageInterface::RESOURCE_ID);
        Assert::keyExists($payload, MessageInterface::RESOURCE_ID_KEY);
        return $payload;
    }
}
