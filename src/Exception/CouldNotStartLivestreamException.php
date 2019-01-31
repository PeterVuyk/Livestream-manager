<?php
declare(strict_types=1);

namespace App\Exception;

class CouldNotStartLivestreamException extends \Exception
{
    const HOST_NOT_AVAILABLE_MESSAGE = 'Unable to start stream because host is not available, aborted';
    const RUN_PROCESS_FAILED_MESSAGE = 'Failed running start stream process, ErrorOutput: %s';
    const INVALID_STATE_OR_CAMERA_STATUS_MESSAGE =
        'Invalid state or camera status message, toStarting: %s cameraStreaming: %s';

    public function __construct(string $reason, \Throwable $previous = null)
    {
        parent::__construct($reason, 0, $previous);
    }

    public static function hostNotAvailable()
    {
        return new self(self::HOST_NOT_AVAILABLE_MESSAGE);
    }

    public static function runProcessFailed(string $errorOutput)
    {
        return new self(sprintf(self::RUN_PROCESS_FAILED_MESSAGE, $errorOutput));
    }

    public static function invalidStateOrCameraStatus(bool $toStarting, bool $cameraStreaming)
    {
        return new self(
            sprintf(self::INVALID_STATE_OR_CAMERA_STATUS_MESSAGE, (string)$toStarting, (string)$cameraStreaming)
        );
    }
}
