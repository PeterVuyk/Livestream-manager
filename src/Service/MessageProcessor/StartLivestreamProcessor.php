<?php
declare(strict_types=1);

namespace App\Service\MessageProcessor;

use App\Entity\StreamSchedule;
use App\Exception\StreamSchedule\ConflictingScheduledStreamsException;
use App\Exception\Repository\CouldNotModifyStreamScheduleException;
use App\Exception\Livestream\ExecutorCouldNotExecuteStreamException;
use App\Repository\StreamScheduleRepository;
use App\Service\LivestreamService;
use App\Service\StreamProcessing\StartLivestream;
use App\Service\StreamProcessing\StreamScheduleExecutor;
use Psr\Log\LoggerInterface;

class StartLivestreamProcessor implements MessageProcessorInterface
{
    /** @var StreamScheduleRepository */
    private $streamScheduleRepository;

    /** @var LoggerInterface */
    private $logger;

    /** @var StartLivestream */
    private $startLivestream;

    /** @var StreamScheduleExecutor */
    private $streamScheduleExecutor;

    /** @var LivestreamService */
    private $livestreamService;

    /**
     * @param StreamScheduleRepository $streamScheduleRepository
     * @param LoggerInterface $logger
     * @param StartLivestream $startLivestream
     * @param StreamScheduleExecutor $streamScheduleExecutor
     * @param LivestreamService $livestreamService
     */
    public function __construct(
        StreamScheduleRepository $streamScheduleRepository,
        LoggerInterface $logger,
        StartLivestream $startLivestream,
        StreamScheduleExecutor $streamScheduleExecutor,
        LivestreamService $livestreamService
    ) {
        $this->streamScheduleRepository = $streamScheduleRepository;
        $this->logger = $logger;
        $this->startLivestream = $startLivestream;
        $this->streamScheduleExecutor = $streamScheduleExecutor;
        $this->livestreamService = $livestreamService;
    }

    public function process(): void
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
                return;
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
}
