<?php
declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class PublishMessageFailedException extends \Exception
{
    public static function forError(string $topicArn, string $message, Throwable $previous = null)
    {
        $message = sprintf('Publish operation has failed, topic: %s, message: %s', $topicArn, $message);
        return new self($message, Response::HTTP_INTERNAL_SERVER_ERROR, $previous);
    }
}
