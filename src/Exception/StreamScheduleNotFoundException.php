<?php
declare(strict_types=1);

namespace App\Exception;

class StreamScheduleNotFoundException extends \Exception
{
    private const TOGGLE_DISABLING_STREAM_SCHEDULE_MESSAGE =
        'Stream schedule not found with id: %s. Could not toggle disabling recurring schedule';

    public function __construct(string $reason, \Throwable $previous = null)
    {
        parent::__construct($reason, 0, $previous);
    }

    public static function couldNotDisableSchedule(string $scheduleId)
    {
        return new self(sprintf(self::TOGGLE_DISABLING_STREAM_SCHEDULE_MESSAGE, $scheduleId));
    }
}
