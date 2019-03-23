<?php
declare(strict_types=1);

namespace App\Tests\App\Messaging\Library\Event;

use App\Exception\Messaging\UnsupportedMessageException;
use App\Messaging\Library\Event\CameraStateChangedEvent;
use App\Messaging\Library\MessageInterface;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

/**
 * @coversDefaultClass \App\Messaging\Library\Event\CameraStateChangedEvent
 * @covers ::<!public>
 * @uses \App\Messaging\Library\Event\Event
 * @uses \App\Messaging\Library\Event\CameraStateChangedEvent
 */
class CameraStateChangedEventTest extends TestCase
{
    /**
     * @dataProvider cameraStateChangedEventProvider
     * @param CameraStateChangedEvent $event
     * @covers ::getCameraState
     */
    public function testGetCameraState(CameraStateChangedEvent $event)
    {
        $this->assertSame('stopping', $event->getCameraState());
    }

    /**
     * @dataProvider cameraStateChangedEventProvider
     * @param CameraStateChangedEvent $event
     * @covers ::getPreviousCameraState
     */
    public function testGetPreviousCameraState(CameraStateChangedEvent $event)
    {
        $this->assertSame('running', $event->getPreviousCameraState());
    }

    /**
     * @dataProvider cameraStateChangedEventProvider
     * @param CameraStateChangedEvent $event
     * @covers ::getResourceId
     */
    public function testGetResourceId(CameraStateChangedEvent $event)
    {
        $this->assertNotNull($event->getResourceId());
        Assert::uuid($event->getResourceId());
    }

    /**
     * @dataProvider cameraStateChangedEventProvider
     * @param CameraStateChangedEvent $event
     * @covers ::getResourceIdKey
     */
    public function testGetResourceIdKey(CameraStateChangedEvent $event)
    {
        $this->assertSame(CameraStateChangedEvent::RESOURCE, $event->getResourceIdKey());
    }

    /**
     * @dataProvider cameraStateChangedEventProvider
     * @param CameraStateChangedEvent $event
     * @covers ::getMessageDate
     */
    public function testGetMessageDate(CameraStateChangedEvent $event)
    {
        $this->assertInstanceOf(\DateTimeInterface::class, $event->getMessageDate());
    }

    /**
     * @dataProvider cameraStateChangedEventProvider
     * @param CameraStateChangedEvent $event
     * @covers ::getPayload
     */
    public function testGetPayload(CameraStateChangedEvent $event)
    {
        $this->assertArrayHasKey(CameraStateChangedEvent::RESOURCE_ID_KEY, $event->getPayload());
        $this->assertArrayHasKey(CameraStateChangedEvent::RESOURCE_ID, $event->getPayload());
        $this->assertArrayHasKey(CameraStateChangedEvent::MESSAGE_DATE, $event->getPayload());
        $this->assertArrayHasKey(CameraStateChangedEvent::USED_MESSAGE_ACTION_KEY, $event->getPayload());
        $this->assertArrayHasKey(CameraStateChangedEvent::PREVIOUS_CAMERA_STATE, $event->getPayload());
        $this->assertArrayHasKey(CameraStateChangedEvent::CAMERA_STATE, $event->getPayload());
    }

    public function cameraStateChangedEventProvider()
    {
        $payload = [
            CameraStateChangedEvent::RESOURCE_ID => (string)Uuid::uuid4(),
            CameraStateChangedEvent::RESOURCE_ID_KEY => CameraStateChangedEvent::RESOURCE,
            CameraStateChangedEvent::MESSAGE_DATE => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
            CameraStateChangedEvent::USED_MESSAGE_ACTION_KEY => CameraStateChangedEvent::USED_MESSAGE_ACTION,
            CameraStateChangedEvent::CAMERA_STATE => 'stopping',
            CameraStateChangedEvent::PREVIOUS_CAMERA_STATE => 'running',
            CameraStateChangedEvent::CHANNEL => 'name',
        ];
        $message = CameraStateChangedEvent::createFromPayload($payload);

        return [[$message]];
    }

    /**
     * @throws UnsupportedMessageException
     * @covers ::createFromPayload
     */
    public function testCreateFromPayload()
    {
        $payload = [
            CameraStateChangedEvent::RESOURCE_ID => (string)Uuid::uuid4(),
            CameraStateChangedEvent::RESOURCE_ID_KEY => CameraStateChangedEvent::RESOURCE,
            CameraStateChangedEvent::MESSAGE_DATE => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
            CameraStateChangedEvent::USED_MESSAGE_ACTION_KEY => CameraStateChangedEvent::USED_MESSAGE_ACTION,
            CameraStateChangedEvent::CAMERA_STATE => 'stopping',
            CameraStateChangedEvent::PREVIOUS_CAMERA_STATE => 'running',
            CameraStateChangedEvent::CHANNEL => 'name',
        ];
        $result = CameraStateChangedEvent::createFromPayload($payload);

        $this->assertInstanceOf(CameraStateChangedEvent::class, $result);
    }

    /**
     * @throws \Exception
     * @covers ::create
     */
    public function testCreate()
    {
        $message = CameraStateChangedEvent::create('prevState', 'newState', 'name');
        $this->assertInstanceOf(MessageInterface::class, $message);
    }
}
