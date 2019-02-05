<?php
declare(strict_types=1);

namespace App\Service\MessageProcessor;

use App\Exception\Messaging\InvalidMessageTypeException;
use App\Messaging\Library\Command\StartLivestreamCommand;
use App\Messaging\Library\Command\StopLivestreamCommand;
use App\Messaging\Library\Event\CameraStateChangedEvent;
use App\Messaging\Library\MessageInterface;

class ProcessMessageDelegator
{
    /** @var StartLivestreamProcessor */
    private $startLivestreamProcessor;

    /** @var StopLivestreamProcessor */
    private $stopLivestreamProcessor;

    /**
     * processMessageDelegator constructor.
     * @param StartLivestreamProcessor $startLivestreamProcessor
     * @param StopLivestreamProcessor $stopLivestreamProcessor
     */
    public function __construct(
        StartLivestreamProcessor $startLivestreamProcessor,
        StopLivestreamProcessor $stopLivestreamProcessor
    ) {
        $this->startLivestreamProcessor = $startLivestreamProcessor;
        $this->stopLivestreamProcessor = $stopLivestreamProcessor;
    }

    /**
     * @param MessageInterface $message
     * @throws InvalidMessageTypeException
     */
    public function process(MessageInterface $message): void
    {
        switch (true) {
            case $message instanceof StartLivestreamCommand:
                $processor = $this->startLivestreamProcessor;
                break;
            case $message instanceof StopLivestreamCommand:
                $processor = $this->stopLivestreamProcessor;
                break;
            case $message instanceof CameraStateChangedEvent:
                return;
            default:
                throw InvalidMessageTypeException::forMessage($message);
        }
        $processor->process($message);
    }
}
