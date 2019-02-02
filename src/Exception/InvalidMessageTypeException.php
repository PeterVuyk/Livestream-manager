<?php
declare(strict_types=1);

namespace App\Exception;

use App\Messaging\Library\MessageInterface;

class InvalidMessageTypeException extends \Exception
{
    const NO_MESSAGE_PROCESSOR_FOR_MESSAGE = 'invalid type, no processor found for: %s';

    public static function forMessage(MessageInterface $message)
    {
        $message = sprintf(self::NO_MESSAGE_PROCESSOR_FOR_MESSAGE, json_encode($message->getPayload()));

        return new self($message);
    }
}
