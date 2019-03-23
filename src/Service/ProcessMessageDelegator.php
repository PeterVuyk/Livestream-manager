<?php
declare(strict_types=1);

namespace App\Service;

use App\Exception\Messaging\InvalidMessageTypeException;
use App\Messaging\Library\Event\CameraStateChangedEvent;
use App\Messaging\Library\MessageInterface;

class ProcessMessageDelegator
{
    /** @var ProcessCameraStateChangedEvent */
    private $processCameraStateChangedEvent;

    /**
     * ProcessMessageDelegator constructor.
     * @param ProcessCameraStateChangedEvent $processCameraStateChangedEvent
     */
    public function __construct(ProcessCameraStateChangedEvent $processCameraStateChangedEvent)
    {
        $this->processCameraStateChangedEvent = $processCameraStateChangedEvent;
    }

    /**
     * @param MessageInterface $message
     * @throws InvalidMessageTypeException
     */
    public function process(MessageInterface $message): void
    {
        switch (true) {
            case $message instanceof CameraStateChangedEvent:
                $processor = $this->processCameraStateChangedEvent;
                break;
            //Later add more processors when needed.
            default:
                throw InvalidMessageTypeException::forMessage($message);
        }
        $processor->process($message);
    }
}
