<?php
declare(strict_types=1);

namespace App\Exception;

class UserNotFoundException extends \Exception
{
    private const REMOVE_USER_MESSAGE = 'User not found with id: %d. Could not remove user';
    private const TOGGLE_DISABLING_USER_MESSAGE = 'User not found with id: %d. Could not toggle disabling user';

    public function __construct(string $reason, \Throwable $previous = null)
    {
        parent::__construct($reason, 0, $previous);
    }

    public static function couldNotRemoveUser(int $userId)
    {
        return new self(sprintf(self::REMOVE_USER_MESSAGE, $userId));
    }

    public static function couldNotToggleDisablingUser(int $userId)
    {
        return new self(sprintf(self::TOGGLE_DISABLING_USER_MESSAGE, $userId));
    }
}
