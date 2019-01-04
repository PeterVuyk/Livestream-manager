<?php
declare(strict_types=1);

namespace App\Exception;

class InvalidConfigurationsException extends \InvalidArgumentException
{
    public static function fromError(\Throwable $previous)
    {
        return new self('Given configurations are invalid', 0, $previous);
    }
}
