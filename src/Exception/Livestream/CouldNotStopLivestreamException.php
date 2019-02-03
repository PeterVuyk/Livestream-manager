<?php
declare(strict_types=1);

namespace App\Exception\Livestream;

class CouldNotStopLivestreamException extends \Exception
{
    const RUN_PROCESS_FAILED_MESSAGE = 'Failed running stop stream process, ErrorOutput: %s';
    const INVALID_STATE_OR_CAMERA_STATUS_MESSAGE =
        'Invalid state or camera status message, toStopping: %s cameraStreaming: %s';

    public function __construct(string $reason, \Throwable $previous = null)
    {
        parent::__construct($reason, 0, $previous);
    }

    public static function invalidStateOrCameraStatus(bool $toStopping, bool $cameraStreaming = null)
    {
        return new self(
            sprintf(self::INVALID_STATE_OR_CAMERA_STATUS_MESSAGE, (string)$toStopping, (string)$cameraStreaming ?? '')
        );
    }

    public static function runProcessFailed(string $errorOutput)
    {
        return new self(sprintf(self::RUN_PROCESS_FAILED_MESSAGE, $errorOutput));
    }
}
