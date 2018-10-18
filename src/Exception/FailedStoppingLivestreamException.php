<?php
declare(strict_types=1);

namespace App\Exception;

class FailedStoppingLivestreamException extends \Exception
{
    private const PI_CAM_MESSAGE = 'Could not stop pi cam.';

    public function __construct(string $reason, \Throwable $previous = null)
    {
        parent::__construct($reason, 0, $previous);
    }

    public static function piCamError(\Throwable $previous = null)
    {
        return new self(self::PI_CAM_MESSAGE, $previous);
    }
}
