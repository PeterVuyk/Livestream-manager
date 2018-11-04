<?php
declare(strict_types=1);

namespace App\Exception;

class StreamScheduleNotFoundException extends \Exception
{
    private const TOGGLE_DISABLING_STREAM_SCHEDULE_MESSAGE =
        'Stream schedule not found with id: %s. Could not toggle disabling recurring schedule';
    private const NEXT_EXECUTION_MESSAGE =
        'Stream schedule not found with id: %s. Could not execute scheduler with next run';
    private const REMOVE_SCHEDULE_MESSAGE =
        'Stream schedule not found with id: %s. Could not remove schedule';
    private const UNWRECK_SCHEDULE_MESSAGE =
        'Stream schedule not found with id: %s. Could not unwreck schedule';

    public function __construct(string $reason, \Throwable $previous = null)
    {
        parent::__construct($reason, 0, $previous);
    }

    public static function couldNotDisableSchedule(string $scheduleId)
    {
        return new self(sprintf(self::TOGGLE_DISABLING_STREAM_SCHEDULE_MESSAGE, $scheduleId));
    }

    public static function couldNotRunWithNextExecution(string $scheduleId)
    {
        return new self(sprintf(self::NEXT_EXECUTION_MESSAGE, $scheduleId));
    }

    public static function couldNotRemoveSchedule(string $scheduleId)
    {
        return new self(sprintf(self::REMOVE_SCHEDULE_MESSAGE, $scheduleId));
    }

    public static function couldNotUnwreckSchedule(string $scheduleId)
    {
        return new self(sprintf(self::UNWRECK_SCHEDULE_MESSAGE, $scheduleId));
    }
}
