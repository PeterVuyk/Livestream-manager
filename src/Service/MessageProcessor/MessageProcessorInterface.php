<?php

namespace App\Service\MessageProcessor;

use App\Messaging\Library\MessageInterface;

interface MessageProcessorInterface
{
    public function process(): void;
}
