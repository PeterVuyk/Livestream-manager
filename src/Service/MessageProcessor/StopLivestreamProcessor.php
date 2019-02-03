<?php
declare(strict_types=1);

namespace App\Service\MessageProcessor;

use App\Entity\StreamSchedule;
use App\Exception\Repository\CouldNotModifyStreamScheduleException;
use App\Exception\Livestream\ExecutorCouldNotExecuteStreamException;
use App\Repository\StreamScheduleRepository;
use App\Service\StreamProcessing\StopLivestream;
use App\Service\StreamProcessing\StreamScheduleExecutor;
use Psr\Log\LoggerInterface;

class StopLivestreamProcessor implements MessageProcessorInterface
{
    /** @var StreamScheduleRepository */
    private $streamScheduleRepository;

    /** @var LoggerInterface */
    private $logger;

    /** @var StopLivestream */
    private $stopLivestream;

    /** @var StreamScheduleExecutor */
    private $streamScheduleExecutor;

    /**
     * @param StreamScheduleRepository $streamScheduleRepository
     * @param LoggerInterface $logger
     * @param StopLivestream $stopLivestream
     * @param StreamScheduleExecutor $streamScheduleExecutor
     */
    public function __construct(
        StreamScheduleRepository $streamScheduleRepository,
        LoggerInterface $logger,
        StopLivestream $stopLivestream,
        StreamScheduleExecutor $streamScheduleExecutor
    ) {
        $this->streamScheduleRepository = $streamScheduleRepository;
        $this->logger = $logger;
        $this->stopLivestream = $stopLivestream;
        $this->streamScheduleExecutor = $streamScheduleExecutor;
    }

    public function process(): void
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
