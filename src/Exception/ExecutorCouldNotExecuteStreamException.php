<?php
declare(strict_types=1);

namespace App\Exception;

class ExecutorCouldNotExecuteStreamException extends \Exception
{
    const FAILED_COMMAND_START_STREAM_MESSAGE = 'Failed to run start stream command, Message: %s';
    const FAILED_COMMAND_STOP_STREAM_MESSAGE = 'Failed to run stop stream command, Message: %s';

    public function __construct(string $reason, \Throwable $previous = null)
    {
        parent::__construct($reason, 0, $previous);
    }

    public static function couldNotStartLivestream(\Throwable $previous)
    {
        $message = sprintf(
            self::FAILED_COMMAND_START_STREAM_MESSAGE,
            $previous->getMessage()
        );
        return new self($message, $previous);
    }

    public static function couldNotStopLivestream(\Throwable $previous)
    {
        $message = sprintf(
            self::FAILED_COMMAND_STOP_STREAM_MESSAGE,
            $previous->getMessage()
        );
        return new self($message, $previous);
    }
}
