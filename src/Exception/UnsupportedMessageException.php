<?php
declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class UnsupportedMessageException extends \Exception
{
    const CLASS_NOT_FOUND_MESSAGE = 'The given message is not supported by deserialize, payload: %s';
    const VALIDATION_FAILED_MESSAGE = 'Could not validate payload: %s, message: %s';

    public static function classNotFound(array $payload)
    {
        $message = sprintf(self::CLASS_NOT_FOUND_MESSAGE, json_encode($payload));

        return new self($message);
    }

    public static function validationFailed(array $payload, \Throwable $previous)
    {
        $message = sprintf(self::VALIDATION_FAILED_MESSAGE, $payload, $previous->getMessage());

        return new self($message, Response::HTTP_INTERNAL_SERVER_ERROR, $previous);
    }
}
