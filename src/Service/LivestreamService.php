<?php
declare(strict_types=1);

namespace App\Service;

use App\Exception\FailedStartingLivestreamException;
use App\Exception\FailedStoppingLivestreamException;

class LivestreamService
{
    /**
     * @throws FailedStartingLivestreamException
     */
    public function startLivestream(): void
    {
        if ($this->isLivestreamRunning()) {
            return;
        }

        try {
            //TODO: run script to start the piCam.
        } catch (\Exception $exception) {
            throw FailedStartingLivestreamException::piCamError($exception);
        }
    }

    /**
     * @throws FailedStoppingLivestreamException
     */
    public function stopLivestream(): void
    {
        if ($this->isLivestreamRunning() === false) {
            return;
        }

        try {
            //TODO: run script to stop the piCam.
        } catch (\Exception $exception) {
            throw FailedStoppingLivestreamException::piCamError($exception);
        }
    }

    /**
     * @return bool
     */
    public function isLivestreamRunning(): bool
    {
        //TODO: check status piCam. return a boolean.
        return false;
    }
}
