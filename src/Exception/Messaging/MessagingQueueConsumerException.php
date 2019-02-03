<?php
declare(strict_types=1);

namespace App\Exception\Messaging;

use Symfony\Component\HttpFoundation\Response;

class MessagingQueueConsumerException extends \Exception
{
    public static function fromError(\Throwable $previous)
    {
        return new self('Could not process message from queue', Response::HTTP_INTERNAL_SERVER_ERROR, $previous);
    }
}
