<?php
declare(strict_types=1);

namespace App\Messaging\Library\Command;

use App\Messaging\Library\MessageInterface;

abstract class Command implements MessageInterface
{
    const USED_MESSAGE_ACTION = 'command';
    const USED_MESSAGE_ACTION_KEY = 'methodAction';

    /**
     * @return string
     */
    public function messageAction(): string
    {
        return self::USED_MESSAGE_ACTION;
    }
}
