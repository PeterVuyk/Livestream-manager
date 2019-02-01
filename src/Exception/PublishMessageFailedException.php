<?php
declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class PublishMessageFailedException extends \Exception
{
    public static function forMessage(string $topicArn, array $payload, Throwable $previous = null)
    {
        $message = sprintf('Publish operation has failed, topic: %s, message: %s', $topicArn, json_encode($payload));
        return new self($message, Response::HTTP_INTERNAL_SERVER_ERROR, $previous);
    }
}
