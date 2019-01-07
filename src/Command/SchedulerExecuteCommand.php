<?php
declare(strict_types=1);

namespace App\Command;

use App\Entity\StreamSchedule;
use App\Exception\ConflictingScheduledStreamsException;
use App\Exception\CouldNotModifyStreamScheduleException;
use App\Exception\ExecutorCouldNotExecuteStreamException;
use App\Service\StopStreamService;
use App\Service\StreamExecutorService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SchedulerExecuteCommand extends Command
{
    const COMMAND_SCHEDULER_EXECUTE = 'scheduler:execute';

    const ERROR_MESSAGE = '<error>%s - Aborted</error>';
    const INFO_MESSAGE = '<info>%s</info>';

    /** @var StreamExecutorService */
    private $streamExecutorService;

    /** @var StopStreamService */
    private $stopStreamService;

    /** @var LoggerInterface */
    private $logger;

    /**
     * SchedulerExecuteCommand constructor.
     * @param StreamExecutorService $streamExecutorService
     * @param StopStreamService $stopStreamService
     * @param LoggerInterface $logger
     */
    public function __construct(
        StreamExecutorService $streamExecutorService,
        StopStreamService $stopStreamService,
        LoggerInterface $logger
    ) {
        $this->streamExecutorService = $streamExecutorService;
        $this->stopStreamService = $stopStreamService;
        $this->logger = $logger;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName(self::COMMAND_SCHEDULER_EXECUTE)
            ->setDescription('Execute scheduled commands.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf(self::INFO_MESSAGE, 'Scheduler execution started'));

        try {
            $streamSchedule = $this->streamExecutorService->getStreamToExecute();
        } catch (ConflictingScheduledStreamsException $exception) {
            $this->logger->error($exception->getMessage(), ['exception' => $exception]);
            $this->stopStreamService->process();
            $output->writeln(sprintf(self::ERROR_MESSAGE, $exception->getMessage()));
            return;
        }

        if (!$streamSchedule instanceof StreamSchedule) {
            $output->writeln(sprintf(self::INFO_MESSAGE, 'No schedules to be executed'));
            return;
        }

        try {
            if ($streamSchedule->streamTobeStarted()) {
                $this->streamExecutorService->start($streamSchedule);
                $output->writeln(sprintf(self::INFO_MESSAGE, 'Livestream successfully started'));
            }
            if ($streamSchedule->streamToBeStopped()) {
                $this->streamExecutorService->stop($streamSchedule);
                $output->writeln(sprintf(self::INFO_MESSAGE, 'Livestream successfully stopped'));
            }
        } catch (ExecutorCouldNotExecuteStreamException $exception) {
            $output->writeln(sprintf(self::ERROR_MESSAGE, $exception->getMessage()));
            $this->logger->error('Could not execute stream command', ['exception' => $exception]);
        } catch (CouldNotModifyStreamScheduleException $exception) {
            $output->writeln(sprintf(self::ERROR_MESSAGE, $exception->getMessage()));
            $this->logger->error('could not update stream schedule', ['message' => $exception->getMessage()]);
        }

        $output->writeln(sprintf(self::INFO_MESSAGE, 'Scheduler execution finished'));
    }
}
