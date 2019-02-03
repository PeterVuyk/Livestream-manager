<?php
declare(strict_types=1);

namespace App\Exception\StreamSchedule;

class CouldNotGetExecutionEndTimeException extends \InvalidArgumentException
{
    public static function forError(\Throwable $previous)
    {
        return new self('Could not get Execution end time, invalid arguments to create an end time', 0, $previous);
    }
}
