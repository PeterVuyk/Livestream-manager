<?php
declare(strict_types=1);

namespace App\Messaging\Library\Command;

use App\Exception\UnsupportedMessageException;
use App\Messaging\Library\MessageInterface;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

class StopLivestreamCommand extends Command
{
    const RESOURCE = 'stopLivestreamCommand';
    const COMMAND_DATE = 'commandDate';

    /** @var string */
    private $resourceId;

    /** @var \DateTimeInterface */
    private $commandDate;

    public static function create(): self
    {
        $self = new self();
        $self->resourceId = (string)Uuid::uuid4();
        $self->commandDate = new \DateTimeImmutable();
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
        $self->commandDate = new \DateTimeImmutable($payload[self::COMMAND_DATE]);
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
    public function getCommandDate(): \DateTimeInterface
    {
        return $this->commandDate;
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
            self::COMMAND_DATE => $this->getCommandDate()->format('Y-m-d H:i:s'),
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
            Assert::keyExists($payload, self::COMMAND_DATE, 'command date from payload is missing');
        } catch (\InvalidArgumentException $exception) {
            throw UnsupportedMessageException::validationFailed($payload, $exception);
        }
    }
}
