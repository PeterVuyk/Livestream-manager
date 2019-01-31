<?php
declare(strict_types=1);

namespace App\Command;

use App\Entity\StreamSchedule;
use App\Exception\CouldNotModifyCameraException;
use App\Exception\CouldNotModifyStreamScheduleException;
use App\Exception\CouldNotStopLivestreamException;
use App\Exception\ExecutorCouldNotExecuteStreamException;
use App\Repository\StreamScheduleRepository;
use App\Service\StreamProcessing\StopLivestream;
use App\Service\StreamProcessing\StreamScheduleExecutor;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StopLivestreamCommand extends Command
{
    const COMMAND_STOP_STREAM = 'stream:stop';

    /** @var StopLivestream */
    private $stopLivestream;

    /** @var StreamScheduleRepository */
    private $streamScheduleRepository;

    /** @var StreamScheduleExecutor */
    private $streamScheduleExecutor;

    /** @var LoggerInterface */
    private $logger;

    /**
     * StopLivestreamCommand constructor.
     * @param StopLivestream $stopLivestream
     * @param StreamScheduleRepository $streamScheduleRepository
     * @param StreamScheduleExecutor $streamScheduleExecutor
     * @param LoggerInterface $logger
     */
    public function __construct(
        StopLivestream $stopLivestream,
        StreamScheduleRepository $streamScheduleRepository,
        StreamScheduleExecutor $streamScheduleExecutor,
        LoggerInterface $logger
    ) {
        parent::__construct();
        $this->stopLivestream = $stopLivestream;
        $this->streamScheduleRepository = $streamScheduleRepository;
        $this->streamScheduleExecutor = $streamScheduleExecutor;
        $this->logger = $logger;
    }

    protected function configure()
    {
        $this
            ->setName(self::COMMAND_STOP_STREAM)
            ->setDescription('Start the livestream.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('Process to stop the livestream started.');

        $streamSchedule = $this->streamScheduleRepository->findRunningSchedule();
        if (!$streamSchedule instanceof StreamSchedule) {
            try {
                //TODO: Send an event instead of calling process directly. Should be a background process.
                $this->stopLivestream->process();
                $output->writeln('Livestream stopped.');
            } catch (CouldNotStopLivestreamException | CouldNotModifyCameraException $exception) {
                $this->logger->error('Could not stop livestream', ['exception' => $exception]);
                $output->writeln('Could not stop livestream.');
            }
            return;
        }

        try {
            $this->streamScheduleExecutor->stop($streamSchedule);
            $output->writeln('Livestream stopped.');
        } catch (ExecutorCouldNotExecuteStreamException | CouldNotModifyStreamScheduleException $exception) {
            $this->logger->error('Could not stop livestream with stream schedule', ['exception' => $exception]);
            $output->writeln('Could not stop livestream.');
        }
    }
}
