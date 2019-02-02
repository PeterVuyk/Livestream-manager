<?php
declare(strict_types=1);

namespace App\Command;

use App\Entity\StreamSchedule;
use App\Exception\ConflictingScheduledStreamsException;
use App\Service\LivestreamService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SchedulerExecuteCommand extends Command
{
    const COMMAND_SCHEDULER_EXECUTE = 'app:scheduler-execute';

    const ERROR_MESSAGE = '<error>%s - Aborted</error>';
    const INFO_MESSAGE = '<info>%s</info>';

    /** @var LoggerInterface */
    private $logger;

    /** @var LivestreamService */
    private $livestreamService;

    /**
     * SchedulerExecuteCommand constructor.
     * @param LoggerInterface $logger
     * @param LivestreamService $livestreamService
     */
    public function __construct(
        LoggerInterface $logger,
        LivestreamService $livestreamService
    ) {
        $this->logger = $logger;
        $this->livestreamService = $livestreamService;
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
            $streamSchedule = $this->livestreamService->getStreamToExecute();
        } catch (ConflictingScheduledStreamsException $exception) {
            $this->logger->warning('conflicting schedules, could not execute', ['exception' => $exception]);
            $output->writeln(sprintf(self::ERROR_MESSAGE, 'conflicting schedules, could not execute'));
            return;
        }

        if (!$streamSchedule instanceof StreamSchedule) {
            $output->writeln(sprintf(self::INFO_MESSAGE, 'No schedules to be executed'));
            return;
        }

        try {
            $this->livestreamService->sendLivestreamCommand($streamSchedule);
        } catch (\Exception $exception) {
            $this->logger->error('Could not publish message', ['exception' => $exception]);
            $output->writeln(sprintf(self::ERROR_MESSAGE, 'Could not publish message'));
        }

        $output->writeln(sprintf(self::INFO_MESSAGE, 'Scheduler execution command send'));
    }
}
