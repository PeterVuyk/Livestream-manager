<?php
declare(strict_types=1);

namespace App\Service;

class StartStreamService implements StreamInterface
{
    public function process(): void
    {
        //TODO: Check if the livestream is running, and if not start the livestream.
    }
}
