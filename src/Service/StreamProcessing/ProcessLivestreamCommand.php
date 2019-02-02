<?php
declare(strict_types=1);

namespace App\Service\StreamProcessing;

use App\Entity\StreamSchedule;
use App\Exception\ConflictingScheduledStreamsException;
use App\Exception\CouldNotModifyStreamScheduleException;
use App\Exception\ExecutorCouldNotExecuteStreamException;
use App\Repository\StreamScheduleRepository;
use App\Service\LivestreamService;
use Psr\Log\LoggerInterface;

class ProcessLivestreamCommand
{
    /** @var StreamScheduleRepository */
    private $streamScheduleRepository;

    /** @var LoggerInterface */
    private $logger;

    /** @var StopLivestream */
    private $stopLivestream;

    /** @var StartLivestream */
    private $startLivestream;

    /** @var StreamScheduleExecutor */
    private $streamScheduleExecutor;

    /** @var LivestreamService */
    private $livestreamService;

    /**
     * ProcessLivestreamCommand constructor.
     * @param StreamScheduleRepository $streamScheduleRepository
     * @param LoggerInterface $logger
     * @param StopLivestream $stopLivestream
     * @param StartLivestream $startLivestream
     * @param StreamScheduleExecutor $streamScheduleExecutor
     * @param LivestreamService $livestreamService
     */
    public function __construct(
        StreamScheduleRepository $streamScheduleRepository,
        LoggerInterface $logger,
        StopLivestream $stopLivestream,
        StartLivestream $startLivestream,
        StreamScheduleExecutor $streamScheduleExecutor,
        LivestreamService $livestreamService
    ) {
        $this->streamScheduleRepository = $streamScheduleRepository;
        $this->logger = $logger;
        $this->stopLivestream = $stopLivestream;
        $this->startLivestream = $startLivestream;
        $this->streamScheduleExecutor = $streamScheduleExecutor;
        $this->livestreamService = $livestreamService;
    }

    public function processStartLivestreamCommand(): void
    {
        try {
            $streamSchedule = $this->livestreamService->getStreamToExecute();
        } catch (ConflictingScheduledStreamsException $exception) {
            $this->logger->warning('conflicting schedules, could not execute', ['exception' => $exception]);
            return;
        }

        if ($streamSchedule instanceof StreamSchedule) {
            try {
                $this->streamScheduleExecutor->start($streamSchedule);
            } catch (CouldNotModifyStreamScheduleException | ExecutorCouldNotExecuteStreamException $exception) {
                //Do nothing, already logged;
                return;
            }
        }

        try {
            $this->startLivestream->process();
        } catch (\Exception $exception) {
            $this->logger->error('Could not start livestream', ['exception' => $exception]);
        }
    }

    public function processStopLivestreamCommand(): void
    {
        $streamSchedule = $this->streamScheduleRepository->findRunningSchedule();
        if ($streamSchedule instanceof StreamSchedule) {
            try {
                $this->streamScheduleExecutor->stop($streamSchedule);
            } catch (CouldNotModifyStreamScheduleException | ExecutorCouldNotExecuteStreamException $exception) {
                //Do nothing, already logged;
                return;
            }
        }

        try {
            $this->stopLivestream->process();
        } catch (\Exception $exception) {
            $this->logger->error('Could not stop livestream', ['exception' => $exception]);
        }
    }
}
