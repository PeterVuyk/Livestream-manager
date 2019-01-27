<?php

namespace App\Entity;

interface StateAwareInterface
{
    public function getState(): string;
    public function setState(string $state): void;
}
