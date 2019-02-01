<?php
declare(strict_types=1);

namespace App\Messaging\Serialize;

use App\Messaging\Library\MessageInterface;

interface DeserializeInterface
{
    public function deserialize(array $payload): MessageInterface;
}
