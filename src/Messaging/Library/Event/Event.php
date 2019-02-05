<?php
declare(strict_types=1);

namespace App\Messaging\Library\Event;

use App\Messaging\Library\MessageInterface;

abstract class Event implements MessageInterface
{
    const USED_MESSAGE_ACTION = 'event';
    const USED_MESSAGE_ACTION_KEY = 'methodAction';

    /**
     * @return string
     */
    public function messageAction(): string
    {
        return self::USED_MESSAGE_ACTION;
    }
}
