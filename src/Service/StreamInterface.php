<?php
declare(strict_types=1);

namespace App\Service;

interface StreamInterface
{
    public function process(): void;
}
