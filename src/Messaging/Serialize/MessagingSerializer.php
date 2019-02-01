<?php
declare(strict_types=1);

namespace App\Messaging\Serialize;

use App\Messaging\Library\MessageInterface;

class MessagingSerializer implements SerializeInterface
{
    /**
     * @param MessageInterface $message
     * @return string
     */
    public function serialize(MessageInterface $message): string
    {
        return json_encode($message->getPayload());
    }
}
