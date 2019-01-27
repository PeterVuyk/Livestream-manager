<?php
declare(strict_types=1);

namespace App\Service\StreamProcessing;

interface StreamInterface
{
    public function process(): void;
}
