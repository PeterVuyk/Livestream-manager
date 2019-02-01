<?php
declare(strict_types=1);

namespace App\Messaging\Serialize;

use App\Messaging\Library\MessageInterface;

interface SerializeInterface
{
    public function serialize(MessageInterface $message): string;
}
