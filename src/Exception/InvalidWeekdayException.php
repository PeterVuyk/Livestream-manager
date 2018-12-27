<?php
declare(strict_types=1);

namespace App\Exception;

class InvalidWeekdayException extends \InvalidArgumentException
{
    const INVALID_DAY_MESSAGE = 'Invalid executionDay input, day: %d';

    public function __construct(string $reason, \Throwable $previous = null)
    {
        parent::__construct($reason, 0, $previous);
    }

    public static function invalidDayInput(int $day)
    {
        return new self(sprintf(self::INVALID_DAY_MESSAGE, $day));
    }
}
