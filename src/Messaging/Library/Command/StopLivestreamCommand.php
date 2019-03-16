<?php
declare(strict_types=1);

namespace App\Messaging\Library\Command;

use App\Exception\Messaging\UnsupportedMessageException;
use App\Messaging\Library\MessageInterface;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

class StopLivestreamCommand extends Command
{
    const RESOURCE = 'stopLivestreamCommand';
    const MESSAGE_DATE = 'messageDate';
    const CHANNEL = 'channel';

    /** @var string */
    private $resourceId;

    /** @var \DateTimeInterface */
    private $messageDate;

    /** @var string */
    private $channel;

    public static function create(string $channel): self
    {
        $self = new self();
        $self->resourceId = (string)Uuid::uuid4();
        $self->channel = $channel;
        $self->messageDate = new \DateTimeImmutable();
        return $self;
    }

    /**
     * @param array $payload
     * @return MessageInterface
     * @throws UnsupportedMessageException
     */
    public static function createFromPayload(array $payload): MessageInterface
    {
        self::validate($payload);
        $self = new self();
        $self->channel = $payload[self::CHANNEL];
        $self->messageDate = new \DateTimeImmutable($payload[self::MESSAGE_DATE]);
        $self->resourceId = $payload[self::RESOURCE_ID];
        return $self;
    }

    /**
     * @return string
     */
    public function getResourceId(): string
    {
        return $this->resourceId;
    }

    /**
     * @return string
     */
    public function getResourceIdKey(): string
    {
        return self::RESOURCE;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getMessageDate(): \DateTimeInterface
    {
        return $this->messageDate;
    }

    /**
     * @return string
     */
    public function getChannel(): string
    {
        return $this->channel;
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return [
            self::USED_MESSAGE_ACTION_KEY => $this->messageAction(),
            self::RESOURCE_ID => $this->getResourceId(),
            self::RESOURCE_ID_KEY => $this->getResourceIdKey(),
            self::CHANNEL => $this->getChannel(),
            self::MESSAGE_DATE => $this->getMessageDate()->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * @param array $payload
     * @throws UnsupportedMessageException
     */
    private static function validate(array $payload): void
    {
        try {
            Assert::keyExists($payload, self::USED_MESSAGE_ACTION_KEY, 'used method action is missing');
            Assert::keyExists($payload, self::RESOURCE_ID, 'Resource id from payload is missing');
            Assert::keyExists($payload, self::RESOURCE_ID_KEY, 'Resource id key from payload is missing');
            Assert::keyExists($payload, self::MESSAGE_DATE, 'message date from payload is missing');
            Assert::keyExists($payload, self::CHANNEL, 'channel from payload is missing');
        } catch (\InvalidArgumentException $exception) {
            throw UnsupportedMessageException::validationFailed($payload, $exception);
        }
    }
}
