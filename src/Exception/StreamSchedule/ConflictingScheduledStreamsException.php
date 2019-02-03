<?php
declare(strict_types=1);

namespace App\Exception\StreamSchedule;

use App\Entity\StreamSchedule;

class ConflictingScheduledStreamsException extends \Exception
{
    const MULTIPLE_SCHEDULES_MESSAGE = 'Multiple schedules to execute the same time, ids: %s';

    public function __construct(string $reason, \Throwable $previous = null)
    {
        parent::__construct($reason, 0, $previous);
    }

    /**
     * @param StreamSchedule[] $streamSchedules
     * @return ConflictingScheduledStreamsException
     */
    public static function multipleSchedules(array $streamSchedules)
    {
        $ids = '';
        foreach ($streamSchedules as $streamSchedule) {
            $ids .= ', ' . $streamSchedule->getId();
        }
        return new self(sprintf(self::MULTIPLE_SCHEDULES_MESSAGE, $ids));
    }
}
