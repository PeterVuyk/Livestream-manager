<?php
declare(strict_types=1);

namespace App\Exception;

use App\Entity\StreamSchedule;

class CouldNotExecuteCommandException extends \Exception
{
    const FAILED_RUNNING_COMMAND_MESSAGE = 'Failed to run command, Command: %s, Message: %s';

    public function __construct(string $reason, \Throwable $previous = null)
    {
        parent::__construct($reason, 0, $previous);
    }

    public static function couldNotRunCommand(StreamSchedule $streamSchedule, \Throwable $previous)
    {
        $message = sprintf(
            self::FAILED_RUNNING_COMMAND_MESSAGE,
            $streamSchedule->getCommand(),
            $previous->getMessage()
        );
        return new self($message, $previous);
    }
}
