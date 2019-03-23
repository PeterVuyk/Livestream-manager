<?php
declare(strict_types=1);

namespace App\Messaging\Library\Event;

use App\Exception\Messaging\UnsupportedMessageException;
use App\Messaging\Library\MessageInterface;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

class CameraStateChangedEvent extends Event
{
    const RESOURCE = 'CameraStateChangedEvent';
    const MESSAGE_DATE = 'messageDate';
    const EVENT_TYPE = 'orderStateChanged';
    const CAMERA_STATE = 'cameraState';
    const PREVIOUS_CAMERA_STATE = 'previousCameraState';
    const CHANNEL = 'channel';

    /** @var string */
    private $cameraState;

    /** @var string */
    private $previousCameraState;

    /** @var string */
    private $resourceId;

    /** @var \DateTimeInterface */
    private $messageDate;

    /** @var string */
    private $channel;

    /**
     * @param array $payload
     * @return MessageInterface
     * @throws UnsupportedMessageException
     */
    public static function createFromPayload(array $payload): MessageInterface
    {
        self::validate($payload);
        $self = new self();
        $self->messageDate = new \DateTimeImmutable($payload[self::MESSAGE_DATE]);
        $self->resourceId = $payload[self::RESOURCE_ID];
        $self->cameraState = $payload[self::CAMERA_STATE];
        $self->previousCameraState = $payload[self::PREVIOUS_CAMERA_STATE];
        $self->channel = $payload[self::CHANNEL];
        return $self;
    }

    /**
     * @param string $previousState
     * @param string $newState
     * @param string $channel
     * @return CameraStateChangedEvent
     */
    public static function create(string $previousState, string $newState, string $channel): MessageInterface
    {
        $self = new self();
        $self->resourceId = (string)Uuid::uuid4();
        $self->messageDate = new \DateTimeImmutable();
        $self->previousCameraState = $previousState;
        $self->cameraState = $newState;
        $self->channel = $channel;

        return $self;
    }

    /**
     * @return string
     */
    public function getCameraState()
    {
        return $this->cameraState;
    }

    /**
     * @return string
     */
    public function getPreviousCameraState()
    {
        return $this->previousCameraState;
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
            self::MESSAGE_DATE => $this->getMessageDate()->format('Y-m-d H:i:s'),
            self::CAMERA_STATE => $this->getCameraState(),
            self::PREVIOUS_CAMERA_STATE => $this->getPreviousCameraState(),
            self::CHANNEL => $this->getChannel(),
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
            Assert::keyExists($payload, self::CAMERA_STATE, 'camera state from payload is missing');
            Assert::keyExists($payload, self::PREVIOUS_CAMERA_STATE, 'previous camera state is missing');
            Assert::keyExists($payload, self::CHANNEL, 'channel is missing');
        } catch (\InvalidArgumentException $exception) {
            throw UnsupportedMessageException::validationFailed($payload, $exception);
        }
    }
}
