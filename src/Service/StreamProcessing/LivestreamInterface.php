<?php
declare(strict_types=1);

namespace App\Service\StreamProcessing;

interface LivestreamInterface
{
    public function process(): void;
}
