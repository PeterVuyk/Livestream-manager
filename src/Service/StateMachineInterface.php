<?php

namespace App\Service;

use App\Entity\StateAwareInterface;

interface StateMachineInterface
{
    public function can(StateAwareInterface $camera, string $transition): bool;

    public function apply(StateAwareInterface $camera, string $transition): void;
}
