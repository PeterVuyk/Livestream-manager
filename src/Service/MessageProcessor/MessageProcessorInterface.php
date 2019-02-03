<?php

namespace App\Service\MessageProcessor;

interface MessageProcessorInterface
{
    public function process(): void;
}
