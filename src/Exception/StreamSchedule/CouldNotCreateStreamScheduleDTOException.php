<?php
declare(strict_types=1);

namespace App\Exception\StreamSchedule;

use App\Entity\StreamSchedule;

class CouldNotCreateStreamScheduleDTOException extends \InvalidArgumentException
{
    const INVALID_ARGUMENTS_MESSAGE = 'Invalid arguments to create DTO object, id: %s, name: %s';

    public function __construct(string $reason, \Throwable $previous = null)
    {
        parent::__construct($reason, 0, $previous);
    }

    public static function invalidArguments(StreamSchedule $streamSchedule, \Throwable $previous)
    {
        return new self(
            sprintf(self::INVALID_ARGUMENTS_MESSAGE, $streamSchedule->getId(), $streamSchedule->getName()),
            $previous
        );
    }
}
